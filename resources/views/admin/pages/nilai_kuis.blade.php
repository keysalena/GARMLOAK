@extends('layouts.app') {{-- Sesuaikan dengan nama layout master admin Anda --}}

@section('content')
<div class="container-fluid pt-4">
    <h1 class="mb-4 text-dark font-weight-bold">Rekap Nilai Kuis Siswa</h1>

    <!-- KOTAK FILTER -->
    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('admin.nilai_kuis.index') }}" method="GET" class="row align-items-end">
                
                <!-- Filter Kelas -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="kelas_id" class="form-label font-weight-bold">Pilih Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $filterKelas == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Modul -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="module_id" class="form-label font-weight-bold">Pilih Modul</label>
                    <select name="module_id" id="module_id" class="form-control">
                        <option value="">-- Semua Modul --</option>
                        @foreach($moduleList as $modul)
                            <option value="{{ $modul->module_id }}" {{ $filterModule == $modul->module_id ? 'selected' : '' }}>
                                {{ $modul->judul }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.nilai_kuis.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA NILAI -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Modul Kuis</th>
                            <th class="text-center">Skor (Nilai)</th>
                            <th>Waktu Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataNilai as $index => $nilai)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-weight-bold">{{ $nilai->nama_siswa ?? 'Siswa Terhapus' }}</td>
                            <td>
                                <span class="badge badge-info px-2 py-1">
                                    {{ $nilai->nama_kelas ?? 'Belum ada kelas' }}
                                </span>
                            </td>
                            <td>{{ $nilai->nama_modul ?? 'Modul Tidak Diketahui' }}</td>
                            <td class="text-center">
                                <h5 class="mb-0 text-success font-weight-bold">{{ $nilai->score }}</h5>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($nilai->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-black-50"></i><br>
                                Belum ada siswa yang mengerjakan kuis pada filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection