@extends('layouts.admin')

@section('title', 'Rekap Nilai Kuis')
@section('breadcrumb')
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li style="color: #2e7d32; font-weight: 600;" aria-current="page">Rekap Nilai Kuis</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Data</p>
        <h1>Rekap Nilai Kuis</h1>
    </div>
</div>

<!-- ========================================== -->
<!-- FILTER -->
<!-- ========================================== -->
<div class="panel-card" style="margin-bottom: 20px;">
    <div class="panel-header">
        <h3>Filter Data</h3>
    </div>

    <form action="{{ route('admin.nilai_kuis.index') }}" method="GET" class="login-form" style="padding: 20px;">
        <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            <label class="field-group" style="flex: 1; min-width: 220px; margin: 0;">
                <span>Pilih Kelas</span>
                <select name="kelas_id" id="kelas_id" style="width: 100%;">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->kelas_id ?? $kelas->id }}" {{ $filterKelas == ($kelas->kelas_id ?? $kelas->id) ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                    @endforeach
                </select>
            </label>

            <label class="field-group" style="flex: 1; min-width: 220px; margin: 0;">
                <span>Pilih Modul</span>
                <select name="module_id" id="module_id" style="width: 100%;">
                    <option value="">Semua Modul</option>
                    @foreach ($moduleList as $modul)
                    <option value="{{ $modul->module_id }}" {{ $filterModule == $modul->module_id ? 'selected' : '' }}>
                        {{ $modul->judul }}
                    </option>
                    @endforeach
                </select>
            </label>

            <div class="action-cell" style="margin: 0;">
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Terapkan Filter</button>
                <a href="{{ route('admin.nilai_kuis.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- TABEL NILAI -->
<!-- ========================================== -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Nilai Kuis</h3>
        <span class="pill neutral">{{ $dataNilai->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>NO</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Modul Kuis</th>
                <th>Skor</th>
                <th>Waktu Pengerjaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataNilai as $nilai)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $nilai->nama_siswa ?? 'Siswa Terhapus' }}</td>
                <td>{{ $nilai->nama_kelas ?? 'Belum ada kelas' }}</td>
                <td>{{ $nilai->nama_modul ?? 'Modul Tidak Diketahui' }}</td>
                <td>
                    <span class="pill neutral" style="font-weight: 700; color: #2e7d32;">{{ $nilai->score }}</span>
                </td>
                <td>{{ \Carbon\Carbon::parse($nilai->tanggal)->translatedFormat('d M Y, H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-state">Belum ada siswa yang mengerjakan kuis pada filter ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // --- LOGIKA LOADING TOMBOL FILTER ---
    function setLoading(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...';
        setTimeout(() => {
            button.disabled = true;
            button.form.submit();
        }, 10);
    }
</script>
@endsection