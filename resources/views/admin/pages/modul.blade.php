@extends('layouts.admin')

@section('title', 'Modul')

@section('content')
<!-- ==========================================
     HEADER & TOMBOL TAMBAH
=========================================== -->
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Konten</p>
        <h1>Kelola Modul</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Modul</button>
</div>

<!-- ==========================================
     TABEL DATA MODUL
=========================================== -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Modul</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Sub CPMK</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->kode_modul }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->sub_cpmk ?? '-' }}</td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <!-- Pada bagian <tbody> tabel modul, ubah <td> aksi menjadi seperti ini: -->
                <td class="action-cell">
                    <!-- Tombol untuk masuk ke materi khusus modul ini -->
                    <a href="{{ route('admin.modul.materi', $item->module_id) }}" class="btn btn-small btn-primary">
                        Kelola Materi
                    </a>

                    <button type="button" class="btn btn-small btn-secondary"
                        onclick="openModal('edit', {{ $item }})">Edit</button>

                    <button type="button" class="btn btn-small btn-danger"
                        data-delete-url="{{ route('admin.modul.destroy', $item->module_id) }}"
                        data-delete-title="Hapus Modul">Hapus</button>

                    <a href="{{ route('admin.modul.questions', $item->module_id) }}" class="btn btn-small btn-primary">
                        Kelola Kuis
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state">Belum ada data modul.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==========================================
     MODAL FORM (TAMBAH / EDIT)
=========================================== -->
<div id="modulModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Modul</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="modulForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Kode Modul</span>
                <input type="text" name="kode_modul" id="inputKodeModul" required>
            </label>

            <label class="field-group">
                <span>Judul</span>
                <input type="text" name="judul" id="inputJudul" required>
            </label>

            <label class="field-group">
                <span>Sub CPMK</span>
                <input type="text" name="sub_cpmk" id="inputSubCpmk">
            </label>

            <label class="field-group">
                <span>Deskripsi</span>
                <textarea name="deskripsi" id="inputDeskripsi" rows="4"></textarea>
            </label>

            <div class="action-cell" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- CSS Modal Overlay -->
<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        overflow-y: auto;
        padding: 20px;
    }
</style>

<!-- ==========================================
     JAVASCRIPT UNTUK MODAL
=========================================== -->
<script>
    const modal = document.getElementById('modulModal');
    const form = document.getElementById('modulForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.modul.store') }}";
    const updateUrlBase = "{{ url('modul') }}"; // Base URL untuk update

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Modul';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk insert
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Modul';
            // Gabungkan Base URL dengan ID Modul
            form.action = `${updateUrlBase}/${data.module_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk update

            // Isi form dengan data yang dilempar dari button Edit
            document.getElementById('inputKodeModul').value = data.kode_modul || '';
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputSubCpmk').value = data.sub_cpmk || '';
            document.getElementById('inputDeskripsi').value = data.deskripsi || '';
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