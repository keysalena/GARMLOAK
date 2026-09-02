<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Materi;
use App\Models\Module;
use App\Models\UserMaterialProgress;
use App\Models\UserModuleProgress;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * @group Modul & Materi
     *
     * Daftar Modul
     *
     * Mendapatkan semua daftar modul beserta persentase progres belajarnya.
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $modules = Module::with(['userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar modul berhasil diambil',
            'data' => ModuleResource::collection($modules),
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Detail Modul
     *
     * Mendapatkan detail satu modul beserta daftar materi dan status penyelesaiannya.
     *
     * @authenticated
     */
    public function show(Request $request, $moduleId)
    {
        $user = $request->user();

        $module = Module::with(['materi' => function ($query) {
            $query->orderBy('urutan', 'asc');
        }])
            ->where('module_id', $moduleId)
            ->firstOrFail();

        $materiProgress = UserMaterialProgress::where('user_id', $user->user_id)
            ->whereIn('materi_id', $module->materi->pluck('materi_id'))
            ->get()
            ->keyBy('materi_id');

        $moduleProgress = UserModuleProgress::where('user_id', $user->user_id)
            ->where('module_id', $module->module_id)
            ->first();

        $materiList = $module->materi->map(function ($item) use ($materiProgress) {
            $progress = $materiProgress->get($item->materi_id);

            return [
                'id' => $item->materi_id,
                'judul' => $item->judul,
                'tipe' => $item->tipe,
                'konten_url' => $item->konten_url,
                'urutan' => $item->urutan,
                'status' => $progress?->status ?? 'belum',
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Detail modul berhasil diambil',
            'data' => [
                'id' => $module->module_id,
                'kode' => $module->kode_modul,
                'judul' => $module->judul,
                'sub_cpmk' => $module->sub_cpmk,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'persen_selesai' => (int) ($moduleProgress?->persen_selesai ?? 0),
                'status' => $moduleProgress?->status ?? 'belum_mulai',
                'materi' => $materiList,
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Detail Materi
     *
     * Memuat isi konten materi sesuai tipe (teks/video/pdf).
     *
     * @authenticated
     */
    public function detail(Request $request, $materiId)
    {
        $materi = Materi::with('module')->where('materi_id', $materiId)->firstOrFail();

        $progress = UserMaterialProgress::where('user_id', $request->user()->user_id)
            ->where('materi_id', $materi->materi_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Detail materi berhasil diambil',
            'data' => [
                'id' => $materi->materi_id,
                'module_id' => $materi->module_id,
                'judul' => $materi->judul,
                'tipe' => $materi->tipe,
                'konten_url' => $materi->konten_url,
                'urutan' => $materi->urutan,
                'status' => $progress?->status ?? 'belum',
                'selesai_pada' => $progress?->selesai_pada,
                'module' => [
                    'id' => $materi->module->module_id,
                    'judul' => $materi->module->judul,
                    'sub_cpmk' => $materi->module->sub_cpmk,
                ],
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Tandai Materi Selesai
     *
     * Menyimpan status materi menjadi selesai dan menghitung ulang progres modul.
     *
     * @authenticated
     */
    public function complete(Request $request, $materiId)
    {
        $user = $request->user();

        $materi = Materi::with('module')->where('materi_id', $materiId)->firstOrFail();

        $progress = UserMaterialProgress::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'materi_id' => $materi->materi_id,
            ],
            [
                'status' => 'selesai',
                'selesai_pada' => now(),
            ]
        );

        $this->recalculateModuleProgress($user->user_id, $materi->module_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Materi berhasil ditandai selesai',
            'data' => [
                'materi_id' => $progress->materi_id,
                'module_id' => $materi->module_id,
                'status' => $progress->status,
                'selesai_pada' => $progress->selesai_pada,
                'persen_selesai' => $this->getModuleProgressPercent($user->user_id, $materi->module_id),
            ],
        ]);
    }

    private function recalculateModuleProgress(string $userId, int $moduleId): void
    {
        $module = Module::with('materi')->where('module_id', $moduleId)->firstOrFail();

        $totalMateri = $module->materi->count();

        if ($totalMateri === 0) {
            $persen = 0;
            $status = 'belum_mulai';
        } else {
            $completed = UserMaterialProgress::where('user_id', $userId)
                ->whereIn('materi_id', $module->materi->pluck('materi_id'))
                ->where('status', 'selesai')
                ->count();

            $persen = (int) round(($completed / $totalMateri) * 100);
            $status = $persen >= 100 ? 'selesai' : ($persen > 0 ? 'berjalan' : 'belum_mulai');
        }

        UserModuleProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'module_id' => $moduleId,
            ],
            [
                'persen_selesai' => $persen,
                'status' => $status,
            ]
        );
    }

    private function getModuleProgressPercent(string $userId, int $moduleId): int
    {
        $progress = UserModuleProgress::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->first();

        return (int) ($progress?->persen_selesai ?? 0);
    }
}
