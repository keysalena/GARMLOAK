<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk query builder
use App\Models\Question;
use Illuminate\Support\Str; // <-- TAMBAHKAN INI

class ModuleController extends Controller
{
    /**
     * Menampilkan Daftar Semua Modul beserta Progres
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Hapus with('ar_content') untuk mencegah error jika relasi Model belum didefinisikan
        $modules = Module::orderBy('urutan', 'asc')->get();

        $modulesData = $modules->map(function ($module) use ($user) {
            
            // Hitung total materi
            $totalMateri = DB::table('materi')
                ->where('module_id', $module->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            // Hitung materi selesai
            $completedMateri = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id)
                ->where('materi.module_id', $module->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            $progress = $totalMateri > 0 ? (float) ($completedMateri / $totalMateri) : 0.0;

            // 2. Ambil data AR secara manual (BYPASS Model)
            $arContent = DB::table('ar_content')
                ->where('module_id', $module->module_id)
                ->orderBy('urutan', 'asc')
                ->get();

            return [
                'module_id' => $module->module_id,
                'kode_modul' => $module->kode_modul,
                'judul' => $module->judul,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'progress' => $progress,
                'ar_content' => $arContent, // Masukkan data AR ke dalam response JSON
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

        // Cari data Modul utama
        $module = Module::where('module_id', $id)->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        // 3. BYPASS RELASI MODEL: Gunakan DB Query langsung agar materi pasti terbaca
        $pendahuluan = DB::table('materi')
            ->where('module_id', $id)
            ->where('tipe', 'pendahuluan')
            ->first();

        $materiList = DB::table('materi')
            ->where('module_id', $id)
            ->where('tipe', '!=', 'pendahuluan')
            ->orderBy('urutan')
            ->get();

        // 4. Ambil ID materi yang sudah dikerjakan
        $materiIds = $materiList->pluck('materi_id')->toArray();
        $completedIds = DB::table('user_progress')
            ->where('user_id', $user->user_id)
            ->whereIn('materi_id', $materiIds)
            ->pluck('materi_id')
            ->map(fn($m_id) => (string) $m_id)
            ->values();

        // 5. Ambil data kuis dan progress-nya
        $kuis = Question::with('options')->where('module_id', $id)->orderBy('urutan')->get();

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
                'kuis' => $kuis,
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
            ->where('user_id', $user->user_id) // Sesuaikan dengan nama primary key di tabel users Anda (user_id atau id)
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
            'user_id' => $user->user_id, // Sesuaikan PK user
            'materi_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Logika Penambahan XP
        $xpGained = 15; // Anda bisa mengubah jumlah XP per materi di sini
        $user->total_xp += $xpGained;

        // 3. Logika Naik Level (Contoh: Setiap kelipatan 500 XP naik 1 level)
        $oldLevel = $user->level;
        $newLevel = floor($user->total_xp / 500) + 1;

        $isLevelUp = $newLevel > $oldLevel;

        if ($isLevelUp) {
            $user->level = $newLevel;
        }

        // 4. SIMPAN PERUBAHAN KE DATABASE (Ini yang sebelumnya terlewat)
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
        // Pastikan model Question Anda memiliki relasi 'options' (hasMany ke QuestionOption)
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
                'id' => (string) Str::uuid(), // Generate UUID manual untuk kolom ID
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
