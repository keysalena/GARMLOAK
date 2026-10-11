<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Question;
use App\Services\ModuleProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /** XP per materi yang diselesaikan. */
    private const XP_PER_MATERI = 15;

    /**
     * Daftar semua modul + progres + konten AR.
     * 4 query total (modul, total materi, materi selesai, ar_content),
     * sebelumnya 3 query x jumlah modul.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $modules = Module::orderBy('urutan', 'asc')
            ->get(['module_id', 'kode_modul', 'judul', 'deskripsi', 'urutan']);

        [$totals, $done] = ModuleProgress::countsForUser($user->user_id);

        // Semua konten AR diambil sekali, lalu dikelompokkan per modul
        $arByModule = DB::table('ar_content')
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('module_id');

        $modulesData = $modules->map(fn ($module) => [
            'module_id' => $module->module_id,
            'kode_modul' => $module->kode_modul,
            'judul' => $module->judul,
            'deskripsi' => $module->deskripsi,
            'urutan' => $module->urutan,
            'progress' => ModuleProgress::ratio(
                (int) $totals->get($module->module_id, 0),
                (int) $done->get($module->module_id, 0)
            ),
            'ar_content' => $arByModule->get($module->module_id, collect())->values(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul berhasil diambil',
            'data' => $modulesData,
        ], 200);
    }

    /**
     * Detail modul (pendahuluan + materi + kuis).
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $module = Module::where('module_id', $id)->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        // Semua materi modul diambil 1x, lalu dipisah di PHP
        $semuaMateri = DB::table('materi')
            ->where('module_id', $id)
            ->orderBy('urutan')
            ->get();

        $pendahuluan = $semuaMateri->firstWhere('tipe', 'pendahuluan');
        $materiList = $semuaMateri->where('tipe', '!=', 'pendahuluan')->values();

        // Materi yang sudah dikerjakan user
        $completedIds = DB::table('user_progress')
            ->where('user_id', $user->user_id)
            ->whereIn('materi_id', $materiList->pluck('materi_id'))
            ->pluck('materi_id')
            ->map(fn ($mId) => (string) $mId)
            ->values();

        // Kuis + opsi (2 query). Kunci jawaban TIDAK dikirim ke aplikasi.
        $kuis = Question::with('options')
            ->where('module_id', $id)
            ->orderBy('urutan')
            ->get();
        $kuis->each(fn ($q) => $q->options->each->makeHidden('is_correct'));

        $moduleProgress = DB::table('user_module_progress')
            ->where('user_id', $user->user_id)
            ->where('module_id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Detail modul berhasil diambil',
            'data' => [
                'module_id' => $module->module_id,
                'kode_modul' => $module->kode_modul,
                'judul' => $module->judul,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'pendahuluan' => $pendahuluan,
                'materi' => $materiList,
                'kuis' => $kuis,
                'completed_materi_ids' => $completedIds,
                'kuis_selesai' => $moduleProgress && $moduleProgress->status === 'selesai',
                'kuis_score' => $moduleProgress ? (int) $moduleProgress->skor_rata_rata : 0,
            ],
        ], 200);
    }

    /**
     * Tandai materi selesai + tambah XP (hanya sekali per materi).
     */
    public function completeMateri(Request $request, $id)
    {
        $user = $request->user();

        $sudahSelesai = fn () => response()->json([
            'success' => true,
            'message' => 'Materi sudah diselesaikan',
            'data' => ['xp_gained' => 0, 'is_level_up' => false, 'level' => $user->level ?? 1],
        ]);

        // Materi harus ada (mencegah XP dari ID asal-asalan)
        if (!DB::table('materi')->where('materi_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan',
            ], 404);
        }

        if (DB::table('user_progress')->where('user_id', $user->user_id)->where('materi_id', $id)->exists()) {
            return $sudahSelesai();
        }

        // insertOrIgnore + unique index (user_id, materi_id) = aman dari klik ganda/request paralel.
        // Hasil 0 berarti request lain sudah lebih dulu menyimpannya.
        $inserted = DB::table('user_progress')->insertOrIgnore([
            'user_id' => $user->user_id,
            'materi_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted === 0) {
            return $sudahSelesai();
        }

        [$isLevelUp] = $this->addXp($user, self::XP_PER_MATERI);

        return response()->json([
            'success' => true,
            'message' => 'Progres tersimpan',
            'data' => [
                'xp_gained' => self::XP_PER_MATERI,
                'is_level_up' => $isLevelUp,
                'level' => $user->level ?? 1,
            ],
        ]);
    }

    /**
     * Submit jawaban kuis. XP hanya diberikan pada penyelesaian PERTAMA modul tersebut;
     * pengulangan kuis tidak menambah XP dan nilai tertinggi yang disimpan.
     */
    public function submitKuis(Request $request, $moduleId)
    {
        $request->validate([
            'jawaban' => 'required|array',
        ]);

        $user = $request->user();
        $jawabanUser = $request->jawaban;

        $kuisList = Question::with('options')
            ->where('module_id', $moduleId)
            ->orderBy('urutan')
            ->get();

        if ($kuisList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.',
            ], 404);
        }

        $abjad = ['a', 'b', 'c', 'd', 'e'];
        $benar = 0;
        $totalSoal = $kuisList->count();

        foreach ($kuisList as $index => $kuis) {
            $correctIndex = $kuis->options->search(fn ($opt) => (bool) $opt->is_correct);
            $kunci = $correctIndex !== false ? ($abjad[$correctIndex] ?? null) : null;

            $jawaban = isset($jawabanUser[$index]) ? strtolower((string) $jawabanUser[$index]) : null;

            if ($kunci !== null && $jawaban === $kunci) {
                $benar++;
            }
        }

        $score = (int) round(($benar / $totalSoal) * 100);

        $result = DB::transaction(function () use ($user, $moduleId, $score) {
            $progress = DB::table('user_module_progress')
                ->where('user_id', $user->user_id)
                ->where('module_id', $moduleId)
                ->lockForUpdate()
                ->first();

            $firstCompletion = !$progress || $progress->status !== 'selesai';

            if ($progress) {
                DB::table('user_module_progress')
                    ->where('id', $progress->id)
                    ->update([
                        'status' => 'selesai',
                        'skor_rata_rata' => max($score, (int) $progress->skor_rata_rata),
                        'persen_selesai' => 100,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('user_module_progress')->insert([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->user_id,
                    'module_id' => $moduleId,
                    'status' => 'selesai',
                    'skor_rata_rata' => $score,
                    'persen_selesai' => 100,
                    'updated_at' => now(),
                ]);
            }

            $xpGained = $firstCompletion ? $score : 0;
            $isLevelUp = false;
            if ($xpGained > 0) {
                [$isLevelUp] = $this->addXp($user, $xpGained);
            }

            return [$xpGained, $isLevelUp];
        });

        [$xpGained, $isLevelUp] = $result;

        return response()->json([
            'success' => true,
            'message' => 'Kuis berhasil diselesaikan.',
            'data' => [
                'score' => $score,
                'xp_gained' => $xpGained,
                'is_level_up' => $isLevelUp,
                'level' => $user->level,
            ],
        ]);
    }

    /**
     * Tambah XP secara atomik di database (aman dari race condition),
     * lalu naikkan level jika perlu.
     *
     * @return array{0: bool} [isLevelUp]
     */
    private function addXp($user, int $xp): array
    {
        $oldLevel = (int) $user->level;

        $user->increment('total_xp', $xp); // UPDATE ... SET total_xp = total_xp + ?

        $newLevel = ModuleProgress::levelFor((int) $user->total_xp);
        $isLevelUp = $newLevel > $oldLevel;

        if ($isLevelUp) {
            $user->level = $newLevel;
            $user->save();
        }

        return [$isLevelUp];
    }
}