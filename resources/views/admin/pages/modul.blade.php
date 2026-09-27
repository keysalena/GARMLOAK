@extends('layouts.admin')

@section('title', 'Modul')
@section('breadcrumb')
    <li style="color: #adb5bd; font-size: 12px;">/</li>
    <li style="color: #2e7d32; font-weight: 600;" aria-current="page">Kelola Modul</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Konten</p>
        <h1>Kelola Modul</h1>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Modul</button>
</div>
@if (session('success'))
    <div style="padding: 16px; background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <strong>✔ Berhasil!</strong> {{ session('success') }}
        </div>
        <button type="button" onclick="this.parentElement.style.display='none';" style="background: transparent; border: none; font-size: 1.2rem; color: #2e7d32; cursor: pointer;">&times;</button>
    </div>
@endif
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

                   <!-- Tombol Edit (Ikon Bulat Abu-abu) -->
                        <button type="button" title="Edit Modul"
                            style="background: transparent; border: 1px solid #d1d5db; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #4b5563; transition: all 0.2s;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"
                            data-item="{{ json_encode($item) }}"
                            onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>

                        <!-- Tombol Hapus (Ikon Bulat Merah) -->
                        <button type="button" title="Hapus Modul"
                            style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #ef4444; transition: all 0.2s;"
                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                            onclick="confirmSingleDelete('{{ route('admin.modul.destroy', $item->module_id) }}', '{{$item->judul }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>

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
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
<!-- Form Hapus Satuan (Tersembunyi) -->
<form id="singleDeleteForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Konfirmasi Hapus Satuan -->
<div id="singleDeleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 400px; margin: 10% auto; text-align: center; padding: 30px 20px;">
        <div style="margin-bottom: 20px;">
            <div style="background-color: #ffebee; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <span style="color: #c62828; font-size: 28px; font-weight: bold;">!</span>
            </div>
            <h3 style="margin-bottom: 10px; color: #333;">Konfirmasi Hapus</h3>
            <p style="color: #6c757d; font-size: 14px; line-height: 1.5;">
                Apakah Anda yakin ingin menghapus modul <strong id="singleDeleteTitleText" style="color: #c62828;"></strong>?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeSingleDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="submitSingleDelete(this)">Ya, Hapus</button>
        </div>
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

        function setLoading(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        setTimeout(() => {
            button.disabled = true;
        }, 10);
    }
    const singleDeleteModal = document.getElementById('singleDeleteModal');
    const singleDeleteForm = document.getElementById('singleDeleteForm');
    const singleDeleteTitleText = document.getElementById('singleDeleteTitleText');

    function confirmSingleDelete(url, namaKelas) {
        // Set URL tujuan form hapus
        singleDeleteForm.action = url;
        // Tampilkan nama kelas di teks modal
        singleDeleteTitleText.innerText = namaKelas;
        // Buka modal
        singleDeleteModal.style.display = 'block';
    }

    function closeSingleDeleteModal() {
        singleDeleteModal.style.display = 'none';
    }

    function submitSingleDelete(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
        button.disabled = true;
        button.previousElementSibling.disabled = true; // Nonaktifkan tombol Batal
        singleDeleteForm.submit();
    }
    // --- TUTUP MODAL JIKA KLIK AREA LUAR ---
    window.onclick = function(event) {
        if (event.target === modal) closeModal();
        if (event.target === singleDeleteModal) closeSingleDeleteModal();
    }
</script>
@endsection