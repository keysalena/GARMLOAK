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
@if ($errors->any())
    <div role="alert" style="padding: 16px; background-color: #fff3f3; color: #b42318; border: 1px solid #fecdca; border-radius: 8px; margin-bottom: 20px;">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif
<style>
    .module-table {
        min-width: 920px;
        table-layout: fixed;
    }

    .module-table th:nth-child(1) { width: 15%; }
    .module-table th:nth-child(2) { width: 37%; }
    .module-table th:nth-child(3) { width: 14%; }
    .module-table th:nth-child(4) { width: 34%; }

    .module-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        min-width: 240px;
    }

    .module-actions .btn {
        justify-content: center;
        min-height: 36px;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .module-ar-link {
        color: #18794e;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 3px;
        white-space: nowrap;
    }

    .module-ar-link:hover {
        color: #105c3a;
    }
</style>
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Modul</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table module-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>AR</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->kode_modul }}</td>
                <td>{{ $item->judul }}</td>
                <td>
                    @php($arLink = $item->arContents->firstWhere('model_3d_url', '!=', null))
                    @if ($arLink)
                        <a href="{{ $arLink->model_3d_url }}" target="_blank" rel="noopener noreferrer" class="module-ar-link">
                            Lihat AR
                        </a>
                    @elseif ($item->arContents->isNotEmpty())
                        <span class="pill neutral">AR terdaftar</span>
                    @else
                        <button type="button" class="btn btn-small btn-primary"
                            data-module-id="{{ $item->module_id }}"
                            data-module-title="{{ $item->judul }}"
                            onclick="openArModal(this)">
                            + Add AR
                        </button>
                    @endif
                </td>
                <td>
                    <div class="module-actions">
                        <a href="{{ route('admin.modul.materi', $item->module_id) }}" class="btn btn-small btn-primary">
                            Kelola Materi
                        </a>
                        <a href="{{ route('admin.modul.questions', $item->module_id) }}" class="btn btn-small btn-primary">
                            Kelola Kuis
                        </a>
                        <button type="button" class="btn btn-small btn-secondary"
                            data-item="{{ json_encode($item) }}"
                            onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">
                            Edit
                        </button>
                        <button type="button" class="btn btn-small btn-danger"
                            data-module-delete-url="{{ route('admin.modul.destroy', $item->module_id) }}"
                            data-delete-title="{{ $item->judul }}"
                            onclick="confirmSingleDelete(this.dataset.moduleDeleteUrl, this.dataset.deleteTitle)">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada data modul.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="arAddModal" class="modal-overlay" style="display: none; z-index: 9999;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 10vh auto; position: relative;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="arAddModalTitle">Tambah AR Content</h3>
            <button type="button" onclick="closeArModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="arAddForm" method="POST" action="{{ route('admin.ar-content.store') }}" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="module_id" id="arAddModuleId">

            <label class="field-group">
                <span>Link Web AR</span>
                <input type="url" name="model_3d_url" id="arAddLink" placeholder="https://..." required>
            </label>

            <div class="action-cell" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeArModal()">Batal</button>
            </div>
        </form>
    </div>
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
                <span>Judul</span>
                <input type="text" name="judul" id="inputJudul" required>
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
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputDeskripsi').value = data.deskripsi || '';
        }

        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    const arAddModal = document.getElementById('arAddModal');
    const arAddForm = document.getElementById('arAddForm');
    const arAddModuleId = document.getElementById('arAddModuleId');
    const arAddLink = document.getElementById('arAddLink');
    const arAddModalTitle = document.getElementById('arAddModalTitle');

    function openArModal(button) {
        arAddForm.reset();
        arAddModuleId.value = button.dataset.moduleId;
        arAddModalTitle.innerText = `Tambah AR - ${button.dataset.moduleTitle}`;
        arAddModal.style.display = 'flex';
    }

    function closeArModal() {
        arAddModal.style.display = 'none';
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
        if (event.target === arAddModal) closeArModal();
    }

    @if (old('module_id') && ($errors->has('module_id') || $errors->has('model_3d_url')))
        const oldArModuleButton = document.querySelector(`[data-module-id="{{ old('module_id') }}"]`);
        if (oldArModuleButton) {
            openArModal(oldArModuleButton);
            arAddLink.value = @json(old('model_3d_url'));
        }
    @endif
</script>
@endsection