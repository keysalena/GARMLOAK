<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Data Beranda (Home)
     * Mengambil profil user dan modul terakhir yang sedang dipelajari.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Logika sederhana penentuan target XP (Bisa disesuaikan dengan aturan gamifikasi Anda)
        $xpTarget = $user->level * 500; 

        // Mencari modul terakhir yang sedang dipelajari (misalnya berdasarkan history progress)
        // Di sini saya asumsikan mengambil modul pertama jika user belum punya progress
        $lastModule = Module::orderBy('urutan', 'asc')->first();
        
        $moduleData = null;
        if ($lastModule) {
            // Simulasi perhitungan persentase progress (bisa diquery dari tabel user_material_progress)
            $progressPercentage = 60; // Hardcode sementara, sesuaikan dengan query DB Anda

            $moduleData = [
                'module_id' => $lastModule->module_id,
                'urutan' => $lastModule->urutan,
                'judul' => $lastModule->judul,
                'progress' => $progressPercentage,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'nama' => $user->nama,
                    'level' => $user->level,
                    'xp' => $user->total_xp,
                    'xp_target' => $xpTarget,
                ],
                'last_module' => $moduleData
            ]
        ]);
    }
}