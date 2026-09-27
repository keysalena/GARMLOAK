<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function completeMateri(Request $request, $materi_id)
    {
        $user = $request->user();
        
        // 1. CEK KEAMANAN: Apakah user sudah pernah menyelesaikan materi ini?
        $isCompleted = DB::table('user_progress')
            ->where('user_id', $user->user_id) // Sesuaikan nama Primary Key user Anda (id / user_id)
            ->where('materi_id', $materi_id)
            ->exists();

        if ($isCompleted) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mendapatkan XP dari materi ini.'
            ], 400); // 400 Bad Request
        }

        // 2. REKAM PROGRES: Kunci materi agar tidak bisa diklaim ulang
        DB::table('user_progress')->insert([
            'user_id' => $user->user_id,
            'materi_id' => $materi_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. TAMBAHKAN XP
        $xpGained = 15; 
        $user->total_xp += $xpGained;

        // 4. LOGIKA LEVELING
        $kalkulasiLevelBaru = floor($user->total_xp / 500) + 1;
        
        $isLevelUp = false;
        if ($kalkulasiLevelBaru > $user->level) {
            $user->level = $kalkulasiLevelBaru;
            $isLevelUp = true;
        }

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