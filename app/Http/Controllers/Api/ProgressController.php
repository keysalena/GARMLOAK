<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Menyelesaikan Materi dan Mendapatkan XP
     */
    public function completeMateri(Request $request, $materi_id)
    {
        $user = $request->user();
        
        // ---------------------------------------------------------
        // PRO TIP: Di sistem produksi nyata, Anda harus mengecek 
        // tabel "user_progress" di database untuk memastikan user 
        // belum pernah menyelesaikan materi ini, agar mereka tidak 
        // bisa spam klik tombol "Selesai" berkali-kali untuk farming XP.
        // ---------------------------------------------------------

        $xpGained = 15; // Jumlah XP yang didapat per materi (bisa disesuaikan)
        
        // Tambahkan XP ke total XP user
        $user->total_xp += $xpGained;

        // Logika Leveling: Target XP = Level Saat Ini * 500
        // Contoh: Level 1 (0 - 499 XP), Level 2 (500 - 999 XP), dst.
        $kalkulasiLevelBaru = floor($user->total_xp / 500) + 1;
        
        $isLevelUp = false;
        if ($kalkulasiLevelBaru > $user->level) {
            $user->level = $kalkulasiLevelBaru;
            $isLevelUp = true;
        }

        // Simpan perubahan ke database
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diselesaikan',
            'data' => [
                'xp_gained' => $xpGained,
                'total_xp' => $user->total_xp,
                'level' => $user->level,
                'is_level_up' => $isLevelUp
            ]
        ], 200);
    }
}