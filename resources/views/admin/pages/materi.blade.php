@extends('layouts.admin')

@section('title', 'Materi')

@section('content')
<!-- ==========================================
     HEADER & TOMBOL TAMBAH
=========================================== -->
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Konten</p>
        <h1>Kelola Materi</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Materi</button>
</div>

<!-- ==========================================
     TABEL DATA MATERI
=========================================== -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Materi</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Modul</th>
                <th>Tipe</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->module?->judul ?? '-' }}</td>
                <td>{{ $item->tipe }}</td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan parameter data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    
                    <!-- Tombol Hapus -->
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.materi.destroy', $item->materi_id) }}" 
                            data-delete-title="Hapus Materi">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state">Belum ada materi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==========================================
     MODAL FORM (TAMBAH / EDIT)
=========================================== -->
<div id="materiModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Materi</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="materiForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Judul</span>
                <input type="text" name="judul" id="inputJudul" required>
            </label>

            <label class="field-group">
                <span>Modul</span>
                <select name="module_id" id="inputModule" required>
                    <option value="" disabled selected>Pilih Modul</option>
                    @foreach ($modules as $module)
                    <option value="{{ $module->module_id }}">{{ $module->judul }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field-group">
                <span>Tipe</span>
                <select name="tipe" id="inputTipe" required>
                    <option value="teks">Teks</option>
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="kuis_singkat">Kuis Singkat</option>
                </select>
            </label>

            <div class="action-cell" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- CSS Modal Overlay (Sesuaikan jika sudah ada di file CSS global) -->
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: flex; align-items: flex-start; justify-content: center;
        overflow-y: auto; padding: 20px;
    }
</style>

<!-- ==========================================
     JAVASCRIPT UNTUK MODAL
=========================================== -->
<script>
    const modal = document.getElementById('materiModal');
    const form = document.getElementById('materiForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.materi.store') }}";
    const updateUrlBase = "{{ url('admin/materi') }}"; // Base URL untuk update

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Materi';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk insert
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Materi';
            // Gabungkan Base URL dengan ID Materi
            form.action = `${updateUrlBase}/${data.materi_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk update
            
            // Isi form dengan data yang dilempar dari button Edit
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputModule').value = data.module_id || '';
            document.getElementById('inputTipe').value = data.tipe || 'teks';
        }
        
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // Tutup modal jika user klik di luar kotak modal
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection