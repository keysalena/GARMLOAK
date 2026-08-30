<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module; // Pastikan model Module sudah di-import
use App\Http\Resources\ModuleResource;
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
    public function index()
    {
        // Ambil semua modul, urutkan berdasarkan field 'urutan'
        $modules = Module::orderBy('urutan', 'asc')->get();

        // Kembalikan respons dalam format JSON menggunakan ModuleResource
        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar modul berhasil diambil',
            'data'    => ModuleResource::collection($modules)
        ]);
    }
}