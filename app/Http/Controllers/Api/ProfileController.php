<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserArProgress;
use App\Models\UserBadge;
use App\Models\UserMaterialProgress;
use App\Models\UserModuleProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load(['kelas', 'levelInfo']);

        $userRank = DB::selectOne(
            'SELECT rank_number
             FROM (
                 SELECT user_id, RANK() OVER (ORDER BY total_xp DESC) AS rank_number
                 FROM users
                 WHERE deleted_at IS NULL
             ) ranked_users
             WHERE user_id = ?',
            [$user->user_id]
        );

        $totalMateri = UserMaterialProgress::where('user_id', $user->user_id)->count();
        $materiSelesai = UserMaterialProgress::where('user_id', $user->user_id)
            ->where('status', 'selesai')
            ->count();

        $arSelesai = UserArProgress::where('user_id', $user->user_id)
            ->where('status', 'selesai')
            ->count();

        $badgesUnlocked = UserBadge::where('user_id', $user->user_id)->count();
        $moduleProgress = UserModuleProgress::where('user_id', $user->user_id)
            ->where('status', 'selesai')
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil user berhasil diambil',
            'data' => [
                'user_id' => $user->user_id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'kelas' => $user->kelas ? [
                    'id' => $user->kelas->kelas_id,
                    'nama' => $user->kelas->nama_kelas,
                ] : null,
                'avatar_url' => $user->avatar_url,
                'level' => [
                    'level_number' => $user->level,
                    'nama_level' => $user->levelInfo?->nama_level,
                    'xp_min' => $user->levelInfo?->xp_min,
                    'xp_max' => $user->levelInfo?->xp_max,
                ],
                'total_xp' => (int) $user->total_xp,
                'rank' => (int) ($userRank?->rank_number ?? 0),
                'statistik' => [
                    'materi_selesai' => $materiSelesai,
                    'total_materi' => $totalMateri,
                    'ar_selesai' => $arSelesai,
                    'badge_terbuka' => $badgesUnlocked,
                    'modul_selesai' => $moduleProgress,
                ],
            ],
        ]);
    }
}
