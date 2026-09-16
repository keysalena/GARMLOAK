<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

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
                'bergabung_sejak' => $user->created_at->format('d M Y'),
            ]
        ], 200);
    }

    /**
     * Mengambil Data Statistik / Progress Belajar
     */
    public function getProgress(Request $request)
    {
        $user = $request->user();

        // Total modul yang ada di sistem
        $totalModules = Module::count();

        // ------------------------------------------------------------------
        // Catatan: Jika Anda memiliki tabel 'user_progress' atau 'user_materi',
        // Anda bisa melakukan query Count() di sini.
        // Untuk saat ini, kita gunakan simulasi data berdasarkan level XP.
        // ------------------------------------------------------------------
        $modulDiselesaikan = floor(($user->total_xp ?? 0) / 100); 
        $persentaseKeseluruhan = $totalModules > 0 ? ($modulDiselesaikan / $totalModules) * 100 : 0;

        return response()->json([
            'success' => true,
            'message' => 'Data progress berhasil diambil',
            'data' => [
                'total_xp' => $user->total_xp ?? 0,
                'level' => $user->level ?? 1,
                'modul_selesai' => $modulDiselesaikan,
                'total_modul' => $totalModules,
                'persentase_keseluruhan' => round($persentaseKeseluruhan),
            ]
        ], 200);
    }
}