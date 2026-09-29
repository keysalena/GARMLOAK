<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk query builder
use App\Models\Question;

class ModuleController extends Controller
{
    /**
     * Menampilkan Daftar Semua Modul beserta Progres
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Mengambil semua modul diurutkan berdasarkan field 'urutan'
        $modules = Module::orderBy('urutan', 'asc')->get();

        // Mapping data untuk menyisipkan perhitungan progres
        $modulesData = $modules->map(function ($module) use ($user) {

            // 1. Hitung total materi reguler (MENGABAIKAN tipe pendahuluan)
            $totalMateri = DB::table('materi')
                ->where('module_id', $module->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            // 2. Hitung materi reguler yang sudah diselesaikan user
            $completedMateri = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id) // Pastikan nama PK user sesuai (user_id / id)
                ->where('materi.module_id', $module->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            // 3. Hitung progres dalam format desimal 0.0 hingga 1.0 (Kebutuhan Flutter)
            // Contoh: 1 selesai dari 4 materi = 0.25
            $progress = $totalMateri > 0 ? (float) ($completedMateri / $totalMateri) : 0.0;

            return [
                'module_id' => $module->module_id,
                'kode_modul' => $module->kode_modul,
                'judul' => $module->judul,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'progress' => $progress, // Nilai progres dikirim ke UI
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

        $module = Module::with([
            'materi',
            'questions.options'
        ])->find($id);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        $pendahuluan = $module->materi->where('tipe', 'pendahuluan')->first();

        // Pastikan urutan materi konsisten (sesuaikan kolom urutan jika ada)
        $materiList = $module->materi
            ->where('tipe', '!=', 'pendahuluan')
            ->sortBy('urutan')
            ->values();

        // Ambil materi_id yang sudah diselesaikan user pada modul ini
        $completedIds = DB::table('user_progress')
            ->where('user_id', $user->user_id)
            ->whereIn('materi_id', $module->materi->pluck('materi_id'))
            ->pluck('materi_id')
            ->map(fn($id) => (string) $id)
            ->values();

        $data = [
            'module_id' => $module->module_id,
            'kode_modul' => $module->kode_modul,
            'judul' => $module->judul,
            'deskripsi' => $module->deskripsi,
            'urutan' => $module->urutan,
            'pendahuluan' => $pendahuluan,
            'materi' => $materiList,
            'kuis' => $module->questions,
            'completed_materi_ids' => $completedIds,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail modul berhasil diambil',
            'data' => $data
        ], 200);
    }
    public function completeMateri(Request $request, $id)
    {
        $user = $request->user();

        $already = DB::table('user_progress')
            ->where('user_id', $user->user_id)
            ->where('materi_id', $id)
            ->exists();

        if ($already) {
            return response()->json([
                'success' => true,
                'message' => 'Materi sudah diselesaikan',
                'data' => ['xp_gained' => 0, 'is_level_up' => false, 'level' => $user->level ?? 1],
            ]);
        }

        DB::table('user_progress')->insert([
            'user_id' => $user->user_id,
            'materi_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sesuaikan dengan logika XP/level yang sudah ada
        $xpGained = 15;
        // ... tambahkan XP ke user, hitung level ...

        return response()->json([
            'success' => true,
            'message' => 'Progres tersimpan',
            'data' => [
                'xp_gained' => $xpGained,
                'is_level_up' => false, // isi dari hasil hitung level
                'level' => $user->level ?? 1,
            ],
        ]);
    }
    public function submitKuis(Request $request, $moduleId)
    {
        // 1. Validasi input dari Flutter
        $request->validate([
            'jawaban' => 'required|array',
        ]);

        // Mengambil data user yang sedang login menggunakan token Sanctum
        $user = $request->user();
        $jawabanUser = $request->jawaban;

        // 2. Ambil data soal kuis berdasarkan module_id
        // Pastikan model 'Kuis' sesuai dengan nama model Anda di Laravel
        $kuisList = Question::where('module_id', $moduleId)->orderBy('id')->get();

        if ($kuisList->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Kuis tidak ditemukan.'
            ], 404);
        }

        // 3. Hitung Jawaban Benar
        $benar = 0;
        $totalSoal = $kuisList->count();

        foreach ($kuisList as $index => $kuis) {
            // Asumsi tabel kuis Anda memiliki kolom 'kunci_jawaban'
            $kunci = strtolower($kuis->kunci_jawaban);
            
            // Cocokkan jawaban berdasarkan index (urutan soal)
            $jawaban = isset($jawabanUser[$index]) ? strtolower($jawabanUser[$index]) : null;

            if ($jawaban === $kunci) {
                $benar++;
            }
        }

        // 4. Hitung Skor (Skala 100)
        $score = (int) round(($benar / $totalSoal) * 100);

        // 5. Tambahkan XP (1 Skor = 1 XP)
        $xpGained = $score;
        $user->total_xp += $xpGained;
        
        // 6. Cek Logika Naik Level (Contoh: Kelipatan 500 XP = 1 Level)
        $oldLevel = $user->level;
        $newLevel = floor($user->total_xp / 500) + 1; 
        
        $isLevelUp = $newLevel > $oldLevel;
        
        if ($isLevelUp) {
            $user->level = $newLevel;
        }
        
        // 7. Simpan perubahan ke database
        $user->save();

        // 8. Kembalikan Response ke aplikasi Flutter
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
