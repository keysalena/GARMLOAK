<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str; 

class ModuleController extends Controller
{
    /**
     * Menampilkan Daftar Semua Modul beserta Progres
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // MENGGUNAKAN EAGER LOADING: Mengambil semua modul beserta relasi materi dan ar_content sekaligus
        // Asumsi: Pada model 'Module', Anda sudah mendaftarkan relasi 'materi' (hasMany) dan 'arContent' (hasMany)
        // Jika belum, buatkan public function materi() dan arContent() di App\Models\Module.php
        $modules = Module::with(['materi' => function($query) {
                // Hanya hitung materi yang bukan pendahuluan
                $query->where('tipe', '!=', 'pendahuluan');
            }, 'arContent' => function($query) {
                $query->orderBy('urutan', 'asc');
            }])
            ->orderBy('urutan', 'asc')
            ->get();

        // MENGAMBIL PROGRESS USER: Sekali query untuk mengambil seluruh ID materi yang sudah diselesaikan user ini
        $completedMateriIds = DB::table('user_progress')
            ->where('user_id', $user->user_id)
            ->pluck('materi_id')
            ->toArray();

        $modulesData = $modules->map(function ($module) use ($completedMateriIds) {
            // Ambil koleksi materi dari relasi (sudah difilter bukan pendahuluan)
            $materi = $module->materi;
            $totalMateri = $materi->count();

            // Hitung berapa materi dari modul ini yang ID-nya ada di dalam array $completedMateriIds
            $completedMateri = $materi->filter(function($m) use ($completedMateriIds) {
                return in_array($m->materi_id, $completedMateriIds);
            })->count();

            $progress = $totalMateri > 0 ? (float) ($completedMateri / $totalMateri) : 0.0;

            return [
                'module_id' => $module->module_id,
                'kode_modul' => $module->kode_modul,
                'judul' => $module->judul,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'progress' => $progress,
                'ar_content' => $module->arContent, // Menggunakan relasi langsung
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul berhasil diambil',
            'data' => $modulesData
        ], 200);
    }

    /**
     * Menampilkan Detail Modul (Materi + Kuis)
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // MENGGUNAKAN EAGER LOADING: Ambil modul berserta materi dan kuis (dan opsi kuis) dalam 1 query utama
        $module = Module::with(['materi' => function($query) {
                $query->orderBy('urutan', 'asc');
            }, 'questions.options'])
            ->where('module_id', $id)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        // Pisahkan materi berdasarkan tipe (memanfaatkan Collection Laravel tanpa query ke DB lagi)
        $materiCollection = $module->materi;
        
        $pendahuluan = $materiCollection->firstWhere('tipe', 'pendahuluan');
        $materiList = $materiCollection->where('tipe', '!=', 'pendahuluan')->values(); // Gunakan .values() untuk me-reset index array

        // Ambil ID materi yang sudah dikerjakan khusus untuk daftar materi yang ada di modul ini saja
        $materiIds = $materiList->pluck('materi_id')->toArray();
        $completedIds = [];
        
        if (!empty($materiIds)) {
            $completedIds = DB::table('user_progress')
                ->where('user_id', $user->user_id)
                ->whereIn('materi_id', $materiIds)
                ->pluck('materi_id')
                ->map(fn($m_id) => (string) $m_id)
                ->values();
        }

        // Ambil status kuis user
        $moduleProgress = DB::table('user_module_progress')
            ->where('user_id', $user->user_id)
            ->where('module_id', $id)
            ->first();

        $isKuisSelesai = $moduleProgress && $moduleProgress->status === 'selesai';
        $kuisScore = $moduleProgress ? (int) $moduleProgress->skor_rata_rata : 0;

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
                'kuis' => $module->questions, // Mengambil langsung dari relasi (Asumsi: ada public function questions() di Module.php)
                'completed_materi_ids' => $completedIds,
                'kuis_selesai' => $isKuisSelesai,
                'kuis_score' => $kuisScore,
            ]
        ], 200);
    }

    public function completeMateri(Request $request, $id)
    {
        $user = $request->user();

        // Cek apakah materi sudah pernah diselesaikan
        $already = DB::table('user_progress')
            ->where('user_id', $user->user_id) 
            ->where('materi_id', $id)
            ->exists();

        // Jika sudah selesai sebelumnya, cegah eksploitasi (jangan tambah XP lagi)
        if ($already) {
            return response()->json([
                'success' => true,
                'message' => 'Materi sudah diselesaikan',
                'data' => ['xp_gained' => 0, 'is_level_up' => false, 'level' => $user->level ?? 1],
            ]);
        }

        // 1. Simpan riwayat penyelesaian materi
        DB::table('user_progress')->insert([
            'user_id' => $user->user_id, 
            'materi_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Logika Penambahan XP
        $xpGained = 15; 
        $user->total_xp += $xpGained;

        // 3. Logika Naik Level
        $oldLevel = $user->level;
        $newLevel = floor($user->total_xp / 500) + 1;

        $isLevelUp = $newLevel > $oldLevel;

        if ($isLevelUp) {
            $user->level = $newLevel;
        }

        // 4. SIMPAN PERUBAHAN KE DATABASE
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Progres tersimpan',
            'data' => [
                'xp_gained' => $xpGained,
                'is_level_up' => $isLevelUp,
                'level' => $user->level ?? 1,
            ],
        ]);
    }

    public function submitKuis(Request $request, $moduleId)
    {
        $request->validate([
            'jawaban' => 'required|array',
        ]);

        $user = $request->user();
        $jawabanUser = $request->jawaban;

        // Ambil data soal beserta opsinya, urutkan berdasarkan kolom 'urutan'
        $kuisList = Question::with('options')->where('module_id', $moduleId)->orderBy('urutan')->get();

        if ($kuisList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.'
            ], 404);
        }

        $benar = 0;
        $totalSoal = $kuisList->count();

        // Array abjad untuk mencocokkan index opsi dari database dengan jawaban Flutter ('a', 'b', 'c', 'd')
        $abjad = ['a', 'b', 'c', 'd', 'e'];

        foreach ($kuisList as $index => $kuis) {
            $correctIndex = -1;

            // Cari opsi mana yang is_correct bernilai true (1) di database
            foreach ($kuis->options as $optIndex => $option) {
                if ($option->is_correct) {
                    $correctIndex = $optIndex;
                    break;
                }
            }

            // Ubah index (misal 0) menjadi abjad (misal 'a') untuk dicocokkan
            $kunci = ($correctIndex !== -1 && isset($abjad[$correctIndex])) ? $abjad[$correctIndex] : null;

            // Ambil jawaban dari Flutter sesuai urutan soal
            $jawaban = isset($jawabanUser[$index]) ? strtolower($jawabanUser[$index]) : null;

            // Jika jawaban cocok, tambah poin benar
            if ($kunci && $jawaban === $kunci) {
                $benar++;
            }
        }

        // Hitung Skor (Skala 100)
        $score = (int) round(($benar / $totalSoal) * 100);

        // --- KODE PERBAIKAN PENYIMPANAN NILAI & STATUS KUIS ---
        $progress = DB::table('user_module_progress')
            ->where('user_id', $user->user_id)
            ->where('module_id', $moduleId)
            ->first();

        if ($progress) {
            // Jika sudah ada, update datanya
            DB::table('user_module_progress')
                ->where('id', $progress->id)
                ->update([
                    'status' => 'selesai',
                    'skor_rata_rata' => $score,
                    'persen_selesai' => 100,
                    'updated_at' => now(),
                ]);
        } else {
            // Jika belum ada, buat row baru beserta UUID manual
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
        // ------------------------------------------------------

        // Tambahkan XP
        $xpGained = $score;
        $user->total_xp += $xpGained;

        // Logika Naik Level (Kelipatan 500 XP = 1 Level)
        $oldLevel = $user->level;
        $newLevel = floor($user->total_xp / 500) + 1;

        $isLevelUp = $newLevel > $oldLevel;

        if ($isLevelUp) {
            $user->level = $newLevel;
        }

        // Simpan perubahan XP dan Level ke tabel users
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kuis berhasil diselesaikan.',
            'data' => [
                'score'       => $score,
                'xp_gained'   => $xpGained,
                'is_level_up' => $isLevelUp,
                'level'       => $user->level
            ]
        ]);
    }
}