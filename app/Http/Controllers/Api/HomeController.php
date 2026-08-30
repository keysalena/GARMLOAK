<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModuleProgress;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data user yang sedang login beserta info batas XP levelnya
        $user = $request->user()->load('levelInfo');

        // 2. Cari progres modul terakhir yang statusnya masih 'berjalan'
        $lanjutkanBelajar = UserModuleProgress::with('module')
            ->where('user_id', $user->user_id)
            ->where('status', 'berjalan')
            ->orderBy('updated_at', 'desc')
            ->first();

        // 3. Susun data agregasi untuk beranda aplikasi
        return response()->json([
            'status' => 'success',
            'data' => [
                'profil_singkat' => [
                    'nama' => $user->nama,
                    'level' => $user->level,
                    'total_xp' => $user->total_xp,
                    'batas_xp' => $user->levelInfo->xp_max ?? 0,
                    'avatar_url' => $user->avatar_url,
                ],
                'lanjutkan_belajar' => $lanjutkanBelajar ? [
                    'module_id' => $lanjutkanBelajar->module_id,
                    'kode_modul' => $lanjutkanBelajar->module->kode_modul ?? '',
                    'judul_modul' => $lanjutkanBelajar->module->judul ?? '',
                    'persen_selesai' => $lanjutkanBelajar->persen_selesai,
                    'icon_url' => $lanjutkanBelajar->module->icon_url ?? null,
                ] : null // Akan mereturn null jika tidak ada modul yang sedang dikerjakan
            ]
        ]);
    }
}