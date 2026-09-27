<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk query builder

class ModuleController extends Controller
{
    /**
     * Menampilkan Daftar Semua Modul beserta Progres
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Mengambil semua modul diurutkan berdasarkan field 'urutan'
        $modules = Module::orderBy('urutan', 'asc')->get();

        // Mapping data untuk menyisipkan perhitungan progres
        $modulesData = $modules->map(function ($module) use ($user) {
            
            // 1. Hitung total materi reguler (MENGABAIKAN tipe pendahuluan)
            $totalMateri = DB::table('materi')
                ->where('module_id', $module->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            // 2. Hitung materi reguler yang sudah diselesaikan user
            $completedMateri = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id) // Pastikan nama PK user sesuai (user_id / id)
                ->where('materi.module_id', $module->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            // 3. Hitung progres dalam format desimal 0.0 hingga 1.0 (Kebutuhan Flutter)
            // Contoh: 1 selesai dari 4 materi = 0.25
            $progress = $totalMateri > 0 ? (float) ($completedMateri / $totalMateri) : 0.0;

            return [
                'module_id' => $module->module_id,
                'kode_modul' => $module->kode_modul,
                'judul' => $module->judul,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'progress' => $progress, // Nilai progres dikirim ke UI
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul berhasil diambil',
            'data' => $modulesData
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
            'questions.options' 
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
        $materiList = $module->materi->where('tipe', '!=', 'pendahuluan')->values();

        // Format data yang akan dikirimkan ke Flutter
        $data = [
            'module_id' => $module->module_id,
            'kode_modul' => $module->kode_modul,
            'judul' => $module->judul,
            'deskripsi' => $module->deskripsi,
            'urutan' => $module->urutan,
            'pendahuluan' => $pendahuluan,
            'materi' => $materiList,
            'kuis' => $module->questions
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail modul berhasil diambil',
            'data' => $data
        ], 200);
    }
}