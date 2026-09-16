@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Header & Aksi Cepat (Kanan Atas) -->
<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    <div>
        <p class="eyebrow text-green">Overview</p>
        <h1>Dashboard Administrator</h1>
        <p style="color: #666; margin-top: 5px;">Ringkasan data dan aktivitas sistem e-learning.</p>
    </div>
    
    <!-- Aksi Cepat -->
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.modul.index') }}" class="btn btn-primary">
            + Tambah Modul
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
            Data Kelas dan Mahasiswa
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
            Marker AR
        </a>
    </div>
</div>

<!-- Statistik Metrik Utama (3 Kolom) -->
<!-- Menggunakan repeat(3, 1fr) agar menjadi 3 kolom ke samping -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px;">
    
    <!-- Stat: Mahasiswa -->
    <div class="stat-card accent-green">
        <div class="stat-top">
            <span class="stat-label">Total Mahasiswa</span>
            <span class="stat-arrow">👥</span>
        </div>
        <strong>{{ $totalMahasiswa }}</strong>
        <small>Terdaftar dalam sistem</small>
    </div>

    <!-- Stat: Kelas -->
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Total Kelas</span>
            <span class="stat-arrow">🏫</span>
        </div>
        <strong>{{ $totalKelas }}</strong>
        <small>Data angkatan aktif</small>
    </div>

    <!-- Stat: Modul -->
    <div class="stat-card soft-card">
        <div class="stat-top">
            <span class="stat-label">Modul Belajar</span>
            <span class="stat-arrow muted">📚</span>
        </div>
        <strong>{{ $totalModul }}</strong>
        <small>Materi utama</small>
    </div>

    <!-- Stat: Materi -->
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Konten Materi</span>
            <span class="stat-arrow">📄</span>
        </div>
        <strong>{{ $totalMateri }}</strong>
        <small>Teks, Video, & PDF</small>
    </div>

    <!-- Stat: Konten AR -->
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Konten AR</span>
            <span class="stat-arrow">📱</span>
        </div>
        <strong>{{ $totalAr }}</strong>
        <small>Marker & 3D Model</small>
    </div>

    <!-- Stat: Soal -->
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Soal Kuis</span>
            <span class="stat-arrow">🎯</span>
        </div>
        <strong>{{ $totalQuestion }}</strong>
        <small>Soal evaluasi modul</small>
    </div>
</div>

<!-- Kolom Konten Bawah (Informasi) -->
<div class="content-grid" style="margin-top: 30px;">
    <!-- Informasi Sistem (Dibuat lebar penuh di bawah) -->
    <div class="panel-card soft-card">
        <div class="panel-header" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            <h3>📌 Informasi & Panduan Sistem</h3>
        </div>
        <div>
            <p style="margin-bottom: 10px;"><strong>Alur Pengelolaan Konten:</strong></p>
            <ol style="padding-left: 20px; color: #555; line-height: 1.6; margin-bottom: 0;">
                <li>Buat <strong>Modul</strong> terlebih dahulu melalui tombol Aksi Cepat.</li>
                <li>Pilih <strong>"Kelola Materi"</strong> pada baris modul tersebut untuk menambahkan materi (Teks, PDF, atau Video).</li>
                <li>Pilih <strong>"Kelola Soal"</strong> pada modul untuk menambahkan pertanyaan dan opsi jawaban (A, B, C, D) untuk kuis.</li>
                <li><strong>Catatan Kuis:</strong> Soal/kuis yang dibuat akan secara otomatis ditampilkan di urutan paling bawah pada aplikasi mahasiswa, tepat setelah semua materi di modul tersebut selesai dipelajari.</li>
            </ol>
        </div>
    </div>
</div>
@endsection