<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Module;

class NilaiKuisController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data untuk opsi Dropdown
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        $moduleList = Module::orderBy('urutan', 'asc')->get();

        // Tangkap pilihan user dari URL/Dropdown
        $filterKelas = $request->kelas_id;
        $filterModule = $request->module_id;

        // Buat query gabungan (Join) ke tabel users, kelas, dan modules
        $query = DB::table('user_module_progress')
            ->join('users', 'user_module_progress.user_id', '=', 'users.user_id')
            ->leftJoin('kelas', 'users.kelas_id', '=', 'kelas.id') // Sesuaikan jika primary key kelas bukan 'id'
            ->join('modules', 'user_module_progress.module_id', '=', 'modules.module_id')
            ->select(
                'users.nama as nama_siswa',
                'kelas.nama_kelas',
                'modules.judul as nama_modul',
                'user_module_progress.skor_rata_rata as score',
                'user_module_progress.updated_at as tanggal'
            )
            ->where('user_module_progress.status', 'selesai');

        // Terapkan Filter jika dipilih
        if (!empty($filterKelas)) {
            $query->where('users.kelas_id', $filterKelas);
        }

        if (!empty($filterModule)) {
            $query->where('user_module_progress.module_id', $filterModule);
        }

        // Ambil hasil query, urutkan dari yang paling baru mengerjakan
        $dataNilai = $query->orderBy('user_module_progress.updated_at', 'desc')->get();

        return view('admin.pages.nilai_kuis', compact('kelasList', 'moduleList', 'dataNilai', 'filterKelas', 'filterModule'));
    }
}