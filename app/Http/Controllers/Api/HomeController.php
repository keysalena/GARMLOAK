<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use App\Services\ModuleProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /** Leaderboard di-cache sebentar: datanya sama untuk semua user. */
    private const LEADERBOARD_TTL = 60; // detik

    public function index(Request $request)
    {
        $user = $request->user();

        // ------------------------------------------------------------
        // 1. Modul + progres: 3 query total (modul, total materi, materi selesai)
        // ------------------------------------------------------------
        $modules = Module::orderBy('urutan', 'asc')->get(['module_id', 'urutan', 'judul']);
        [$totals, $done] = ModuleProgress::countsForUser($user->user_id);

        $targetModule = null;
        $modulSelesaiCount = 0;

        foreach ($modules as $module) {
            $total = (int) $totals->get($module->module_id, 0);
            $completed = (int) $done->get($module->module_id, 0);
            $progress = ModuleProgress::ratio($total, $completed);

            if ($total > 0 && $total === $completed) {
                $modulSelesaiCount++;
            }

            if ($progress < 1.0 && $targetModule === null) {
                $targetModule = [
                    'module_id' => $module->module_id,
                    'urutan' => $module->urutan,
                    'judul' => $module->judul,
                    'progress' => $progress,
                ];
            }
        }

        // Semua modul selesai -> tampilkan modul terakhir
        if ($targetModule === null && $modules->isNotEmpty()) {
            $last = $modules->last();
            $targetModule = [
                'module_id' => $last->module_id,
                'urutan' => $last->urutan,
                'judul' => $last->judul,
                'progress' => 1.0,
            ];
        }

        // ------------------------------------------------------------
        // 2. Leaderboard (di-cache)
        // ------------------------------------------------------------
        $leaderboardGlobal = Cache::remember('leaderboard:global', self::LEADERBOARD_TTL, fn () =>
            $this->topUsers(User::where('role', 'mahasiswa'))
        );

        $leaderboardKelas = [];
        if ($user->kelas_id !== null) {
            $leaderboardKelas = Cache::remember("leaderboard:kelas:{$user->kelas_id}", self::LEADERBOARD_TTL, fn () =>
                $this->topUsers(
                    User::where('kelas_id', $user->kelas_id)->where('role', 'mahasiswa')
                )
            );
        }

        // ------------------------------------------------------------
        // 3. Response
        // ------------------------------------------------------------
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'nama' => $user->nama,
                    'level' => $user->level,
                    'xp' => $user->total_xp,
                    'avatar_url' => $user->avatar_url ? url('storage/' . $user->avatar_url) : null,
                    'xp_target' => $user->level * 500,
                ],
                'last_module' => $targetModule,
                'modul_selesai' => $modulSelesaiCount,
                'total_modul' => $modules->count(),
                'leaderboard_global' => $leaderboardGlobal,
                'leaderboard_kelas' => $leaderboardKelas,
            ],
        ], 200);
    }

    /** Top 3 user berdasarkan XP, sebagai array biasa (aman untuk di-cache). */
    private function topUsers($query): array
    {
        return $query->orderBy('total_xp', 'desc')
            ->take(3)
            ->get(['nama', 'total_xp', 'level'])
            ->map(fn ($u, $i) => [
                'rank' => $i + 1,
                'nama' => $u->nama,
                'xp' => $u->total_xp,
                'level' => $u->level,
            ])
            ->all();
    }
}