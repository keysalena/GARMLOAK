<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $progress = $this->userProgress ?? null;

        return [
            'id' => $this->module_id,
            'kode' => $this->kode_modul,
            'judul' => $this->judul,
            'sub_cpmk' => $this->sub_cpmk,
            'urutan' => $this->urutan,
            'persen_selesai' => (int) ($progress?->persen_selesai ?? 0),
            'status' => $progress?->status ?? 'belum_mulai',
            // Jika nanti ada URL gambar/icon, bisa difilter di sini agar menjadi URL lengkap
            // 'icon_url' => $this->icon_url ? url('storage/' . $this->icon_url) : null,
        ];
    }
}
