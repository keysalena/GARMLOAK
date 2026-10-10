<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ==========================================
        // 1. AMBIL SEMUA MODUL & HITUNG PROGRES
        // ==========================================
        $modules = Module::orderBy('urutan', 'asc')->get();

        $targetModule = null;
        $modulSelesaiCount = 0;
        $totalModul = $modules->count();

        foreach ($modules as $module) {

            $totalMateri = DB::table('materi')
                ->where('module_id', $module->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            $completedMateri = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id)
                ->where('materi.module_id', $module->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            $progress = $totalMateri > 0 ? (float) ($completedMateri / $totalMateri) : 0.0;

            if ($totalMateri > 0 && $totalMateri == $completedMateri) {
                $modulSelesaiCount++;
            }

            $moduleData = [
                'module_id' => $module->module_id,
                'urutan' => $module->urutan,
                'judul' => $module->judul,
                'progress' => $progress,
            ];

            if ($progress < 1.0 && is_null($targetModule)) {
                $targetModule = $moduleData;
            }
        }

        if (is_null($targetModule) && $totalModul > 0) {
            $last = $modules->last();
            $targetModule = [
                'module_id' => $last->module_id,
                'urutan' => $last->urutan,
                'judul' => $last->judul,
                'progress' => 1.0,
            ];
        }

        // ==========================================
        // 2. LOGIKA LEADERBOARD (Berdasarkan skema DB)
        // ==========================================

        // A. Leaderboard Global (Hanya role 'mahasiswa')
        $leaderboardGlobal = User::where('role', 'mahasiswa')
            ->orderBy('total_xp', 'desc')
            ->take(3)
            ->get(['nama', 'total_xp', 'level'])
            ->map(function ($u, $index) {
                return [
                    'rank' => $index + 1,
                    'nama' => $u->nama,
                    'xp' => $u->total_xp,
                    'level' => $u->level
                ];
            });

        // B. Leaderboard Per Kelas
        // Menggunakan 'kelas_id' sesuai dengan skema tabel users Anda
        $kelasIdUser = $user->kelas_id;
        $leaderboardKelas = [];

        if ($kelasIdUser != null) {
            $leaderboardKelas = User::where('kelas_id', $kelasIdUser)
                ->where('role', 'mahasiswa') // Pastikan admin kelas tidak masuk
                ->orderBy('total_xp', 'desc')
                ->take(3)
                ->get(['nama', 'total_xp', 'level'])
                ->map(function ($u, $index) {
                    return [
                        'rank' => $index + 1,
                        'nama' => $u->nama,
                        'xp' => $u->total_xp,
                        'level' => $u->level
                    ];
                });
        }

        // ==========================================
        // 3. KIRIM DATA KE FLUTTER
        // ==========================================
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
                'total_modul' => $totalModul,
                'leaderboard_global' => $leaderboardGlobal,
                'leaderboard_kelas' => $leaderboardKelas
            ]
        ], 200);
    }
}
