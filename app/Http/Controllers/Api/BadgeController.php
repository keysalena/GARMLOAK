<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $unlockedIds = UserBadge::where('user_id', $user->user_id)
            ->pluck('badge_id')
            ->toArray();

        $badges = Badge::orderBy('badge_id', 'asc')->get()->map(function ($badge) use ($unlockedIds) {
            return [
                'id' => $badge->badge_id,
                'nama' => $badge->nama,
                'deskripsi' => $badge->deskripsi,
                'icon_url' => $badge->icon_url,
                'kriteria' => $badge->kriteria,
                'status' => in_array($badge->badge_id, $unlockedIds, true) ? 'unlocked' : 'locked',
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Badge berhasil diambil',
            'data' => $badges,
        ]);
    }
}
