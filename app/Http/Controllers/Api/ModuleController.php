<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Menampilkan Daftar Semua Modul
     */
    public function index()
    {
        // Mengambil semua modul diurutkan berdasarkan field 'urutan'
        $modules = Module::orderBy('urutan', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul berhasil diambil',
            'data' => $modules
        ], 200);
    }

    /**
     * Menampilkan Detail Modul (Materi + Kuis)
     */
    public function show($id)
    {
        // Mencari modul beserta relasi materi dan soal kuisnya
        $module = Module::with([
            'materi', 
            'questions.options' // Load soal sekaligus dengan opsi jawabannya (A,B,C,D)
        ])->find($id);

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        // 1. Ambil SATU materi yang tipenya pendahuluan sebagai pendahuluan
        $pendahuluan = $module->materi->where('tipe', 'pendahuluan')->first();
        
        // 2. Ambil SISANYA (yang bukan pendahuluan) sebagai list materi biasa.
        // Gunakan values() agar format JSON array-nya tetap rapi (tidak berubah jadi object index)
        $materiList = $module->materi->where('tipe', '!=', 'pendahuluan')->values();

        // Format data yang akan dikirimkan ke Flutter
        $data = [
            'module_id' => $module->module_id,
            'kode_modul' => $module->kode_modul,
            'judul' => $module->judul,
            'deskripsi' => $module->deskripsi,
            'urutan' => $module->urutan,
            
            // Pendahuluan terpisah di luar list (Bisa null jika tidak ada)
            'pendahuluan' => $pendahuluan,
            
            // List materi reguler (Array murni)
            'materi' => $materiList,
            
            // List soal untuk evaluasi
            'kuis' => $module->questions
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail modul berhasil diambil',
            'data' => $data
        ], 200);
    }
}