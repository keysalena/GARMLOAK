<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk query builder

class ProfileController extends Controller
{
    /**
     * Mengambil Data Profil Pengguna
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'nama' => $user->nama, // Sesuaikan dengan nama kolom di DB Anda
                'email' => $user->email,
                'level' => $user->level ?? 1,
                'xp' => $user->total_xp ?? 0,
                'xp_target' => ($user->level ?? 1) * 500,
                'bergabung_sejak' => $user->created_at->translatedFormat('F Y')
            ]
        ], 200);
    }

    /**
     * Mengambil Data Statistik / Progress Belajar
     */
    public function getProgress(Request $request)
    {
        $user = $request->user();

        // 1. Hitung Total Modul Keseluruhan
        $totalModules = Module::count();

        // 2. Hitung Total Materi Keseluruhan di Aplikasi (Abaikan pendahuluan)
        $totalMateri = DB::table('materi')->where('tipe', '!=', 'pendahuluan')->count();

        // 3. Hitung Total Materi yang Selesai Dikerjakan User Ini (Abaikan pendahuluan)
        $materiSelesai = DB::table('user_progress')
            ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
            ->where('user_progress.user_id', $user->user_id) // Sesuaikan user_id atau id
            ->where('materi.tipe', '!=', 'pendahuluan')
            ->count();

        // 4. Hitung Persentase Akurat (0 - 100)
        $persentaseKeseluruhan = $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0;

        // 5. Hitung Jumlah Modul yang Benar-Benar 100% Selesai
        $modules = DB::table('modules')->get();
        $modulSelesaiCount = 0;

        foreach ($modules as $mod) {
            // Berapa target materi reguler di modul ini?
            $jmlMateriModul = DB::table('materi')
                ->where('module_id', $mod->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            // Berapa materi reguler di modul ini yang sudah di-klik selesai oleh user?
            $jmlMateriUserModul = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id)
                ->where('materi.module_id', $mod->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            // Jika modul memiliki materi (tidak kosong) DAN jumlah materi yg selesai SAMA DENGAN target materi
            if ($jmlMateriModul > 0 && $jmlMateriModul == $jmlMateriUserModul) {
                $modulSelesaiCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data progress berhasil diambil',
            'data' => [
                'total_xp' => $user->total_xp ?? 0,
                'level' => $user->level ?? 1,
                'modul_selesai' => $modulSelesaiCount,
                'total_modul' => $totalModules,
                'persentase_keseluruhan' => $persentaseKeseluruhan,
            ]
        ], 200);
    }
}
