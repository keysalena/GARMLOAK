@extends('layouts.admin')

@section('title', 'Kelas')
@section('breadcrumb')
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li style="color: #2e7d32; font-weight: 600;" aria-current="page">Kelola Kelas</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Data</p>
        <h1>Kelola Kelas</h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <!-- Tombol Hapus Massal (Disembunyikan secara default) -->
        <button type="button" class="btn btn-danger" id="btnBulkDelete" style="display: none;" onclick="confirmBulkDelete()">
            Hapus Terpilih (<span id="selectedCount">0</span>)
        </button>
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Kelas</button>
    </div>
</div>

<!-- ========================================== -->
<!-- ALERT BERHASIL & ERROR (Auto-Close) -->
<!-- ========================================== -->
@if (session('success'))
<div class="auto-close-alert" style="padding: 16px; background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; transition: opacity 0.5s ease;">
    <div><strong>✔ Berhasil!</strong> {{ session('success') }}</div>
    <button type="button" onclick="this.parentElement.style.display='none';" style="background: transparent; border: none; font-size: 1.2rem; color: #2e7d32; cursor: pointer;">&times;</button>
</div>
@endif

@if (session('error'))
<div class="auto-close-alert" style="padding: 16px; background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; transition: opacity 0.5s ease;">
    <div><strong>❌ Gagal!</strong> {{ session('error') }}</div>
    <button type="button" onclick="this.parentElement.style.display='none';" style="background: transparent; border: none; font-size: 1.2rem; color: #c62828; cursor: pointer;">&times;</button>
</div>
@endif

@if ($errors->any())
<div class="auto-close-alert" style="padding: 16px; background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: flex-start; justify-content: space-between; transition: opacity 0.5s ease;">
    <div>
        <strong style="display: block; margin-bottom: 5px;">❌ Terjadi Kesalahan!</strong>
        <ul style="margin: 0 0 0 20px; padding: 0; font-size: 0.9rem;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" onclick="this.parentElement.style.display='none';" style="background: transparent; border: none; font-size: 1.2rem; color: #c62828; cursor: pointer;">&times;</button>
</div>
@endif

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Kelas</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <!-- Form untuk hapus massal -->
    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.kelas.bulk_destroy') }}">
        @csrf
        @method('DELETE')
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAll" style="cursor: pointer; width: 16px; height: 16px;">
                    </th>
                    <th>NO</th>
                    <th>Nama Kelas</th>
                    <th>Angkatan</th>
                    <th>Jumlah Mahasiswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" name="ids[]" value="{{ $item->kelas_id }}" class="row-checkbox" style="cursor: pointer; width: 16px; height: 16px;">
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_kelas }}</td>
                    <td>{{ $item->tahun_ajaran ?? '-' }}</td>
                    <td>{{ $item->mahasiswa_count ?? 0 }} Orang</td>
                    <td class="action-cell">
                        <a href="{{ route('admin.kelas.mahasiswa', $item->kelas_id) }}" class="btn btn-small btn-primary">Kelola Mahasiswa</a>

                       <!-- Tombol Edit (Ikon Bulat Abu-abu) -->
                        <button type="button" title="Edit Mahasiswa"
                            style="background: transparent; border: 1px solid #d1d5db; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #4b5563; transition: all 0.2s;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"
                            data-item="{{ json_encode($item) }}"
                            onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>

                        <!-- Tombol Hapus (Ikon Bulat Merah) -->
                        <button type="button" title="Hapus Mahasiswa"
                            style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #ef4444; transition: all 0.2s;"
                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                            onclick="confirmSingleDelete('{{ route('admin.kelas.destroy', $item->kelas_id) }}', '{{ $item->nama_kelas }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">Belum ada data kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </form>
</div>

<!-- Modal Form Kelas -->
<div id="kelasModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Kelas</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="kelasForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Nama Kelas</span>
                <input type="text" name="nama_kelas" id="inputNamaKelas" required>
            </label>

            <label class="field-group">
                <span>Angkatan</span>
                <input type="text" name="tahun_ajaran" id="inputTahunAjaran">
            </label>

            <div class="action-cell" style="margin-top: 20px;">
                <!-- PERUBAHAN: type="submit" menjadi type="button" -->
                <button type="button" class="btn btn-primary" onclick="setLoading(this)">Simpan</button>
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
                Apakah Anda yakin ingin menghapus kelas <strong id="singleDeleteTitleText" style="color: #c62828;"></strong>?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeSingleDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="submitSingleDelete(this)">Ya, Hapus</button>
        </div>
    </div>
</div>
<!-- Modal Konfirmasi Hapus Massal -->
<div id="bulkDeleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 400px; margin: 10% auto; text-align: center; padding: 30px 20px;">
        <div style="margin-bottom: 20px;">
            <div style="background-color: #ffebee; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <span style="color: #c62828; font-size: 28px; font-weight: bold;">!</span>
            </div>
            <h3 style="margin-bottom: 10px; color: #333;">Konfirmasi Hapus</h3>
            <p style="color: #6c757d; font-size: 14px; line-height: 1.5;">
                Apakah Anda yakin ingin menghapus <strong id="bulkDeleteCountText" style="color: #c62828;">0</strong> kelas yang dipilih beserta seluruh mahasiswanya?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeBulkDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="submitBulkDelete(this)">Ya, Hapus</button>
        </div>
    </div>
</div>

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

<script>
    // --- LOGIKA AUTO CLOSE ALERT ---
    setTimeout(function() {
        const alerts = document.querySelectorAll('.auto-close-alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 10000);

    // --- LOGIKA MODAL KELAS ---
    const modal = document.getElementById('kelasModal');
    const form = document.getElementById('kelasForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const storeUrl = "{{ route('admin.kelas.store') }}";
    const updateUrlBase = "{{ url('kelas') }}"; // Sesuaikan jika ada prefix admin/kelas

    function openModal(mode, data = null) {
        form.reset();
        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Kelas';
            form.action = storeUrl;
            formMethod.value = 'POST';
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Kelas';
            form.action = `${updateUrlBase}/${data.kelas_id}`;
            formMethod.value = 'PUT';
            document.getElementById('inputNamaKelas').value = data.nama_kelas || '';
            document.getElementById('inputTahunAjaran').value = data.tahun_ajaran || '';
        }
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // --- LOGIKA LOADING TOMBOL SIMPAN ---
    function setLoading(button) {
        if (form.checkValidity()) {
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            setTimeout(() => {
                button.disabled = true;
                form.submit();
            }, 10);
        } else {
            form.reportValidity();
        }
    }

    // --- LOGIKA BULK DELETE & CHECKBOX ---
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // Variabel Modal Hapus Massal
    const bulkModal = document.getElementById('bulkDeleteModal');
    const bulkDeleteCountText = document.getElementById('bulkDeleteCountText');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteButton();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteButton);
    });

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCount.innerText = checkedCount;

        if (checkedCount > 0) {
            btnBulkDelete.style.display = 'inline-block';
        } else {
            btnBulkDelete.style.display = 'none';
        }

        if (checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0) {
            selectAll.checked = true;
        } else if (selectAll) {
            selectAll.checked = false;
        }
    }

    // Tampilkan Modal Hapus Massal
    function confirmBulkDelete() {
        bulkDeleteCountText.innerText = selectedCount.innerText;
        bulkModal.style.display = 'block';
    }

    // Tutup Modal Hapus Massal
    function closeBulkDeleteModal() {
        bulkModal.style.display = 'none';
    }

    // Eksekusi Hapus Massal
    function submitBulkDelete(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
        button.disabled = true;
        button.previousElementSibling.disabled = true; // Nonaktifkan tombol Batal
        bulkDeleteForm.submit();
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
        if (event.target === bulkModal) closeBulkDeleteModal();
        if (event.target === singleDeleteModal) closeSingleDeleteModal();
    }
</script>
@endsection