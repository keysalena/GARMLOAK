<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'global');
        $user = $request->user();

        $query = User::query()
            ->whereNull('deleted_at')
            ->orderByDesc('total_xp')
            ->orderBy('nama', 'asc');

        if ($type === 'kelas') {
            $query->where('kelas_id', $user->kelas_id);
        }

        $leaderboard = $query->get()->map(function ($entry, $index) use ($user) {
            return [
                'rank' => $index + 1,
                'user_id' => $entry->user_id,
                'nama' => $entry->nama,
                'kelas_id' => $entry->kelas_id,
                'total_xp' => (int) $entry->total_xp,
                'is_current_user' => $entry->user_id === $user->user_id,
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Leaderboard berhasil diambil',
            'data' => [
                'type' => $type,
                'items' => $leaderboard,
            ],
        ]);
    }
}
