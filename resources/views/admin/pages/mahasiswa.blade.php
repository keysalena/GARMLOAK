@extends('layouts.admin')

@section('title', 'Data Mahasiswa')
@section('breadcrumb')
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li>
    <a href="{{ route('admin.kelas.index') }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Kelola Kelas</a>
</li>
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li style="color: #2e7d32; font-weight: 600;" aria-current="page">Kelola Mahasiswa</li>
@endsection

@section('content')
<!-- Library DataTables & jQuery -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div class="page-header">
    <div>
        <p class="eyebrow text-green">Kelas: {{ $kelas->nama_kelas }} ({{$kelas->tahun_ajaran }})</p>
        <h1>Daftar Mahasiswa</h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-danger" id="btnBulkDelete" style="display: none;" onclick="confirmBulkDelete()">
            Hapus Terpilih (<span id="selectedCount">0</span>)
        </button>

        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Kembali ke Kelas</a>
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Mahasiswa</button>
    </div>
</div>

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

<div class="panel-card" style="padding: 25px;">
    <!-- Penyesuaian Header Tabel (Judul + Total) -->
    <div class="panel-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 25px; border-bottom: none; padding-bottom: 0;">
        <h3 style="margin: 0; font-size: 1.2rem; color: #111827;">Mahasiswa Terdaftar</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>    
    </div>

    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.mahasiswa.bulk_destroy') }}">
        @csrf
        @method('DELETE')
        <table class="data-table" id="tableMahasiswa" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f9fafb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="width: 40px; text-align: center; padding: 12px; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                        <input type="checkbox" id="selectAll" style="cursor: pointer; width: 16px; height: 16px; border-radius: 4px; border-color: #d1d5db;">
                    </th>
                    <th style="width: 50px; padding: 12px; border-bottom: 1px solid #e5e7eb;">NO</th>
                    <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">NAMA</th>
                    <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">EMAIL</th>
                    <th style="padding: 12px; border-bottom: 1px solid #e5e7eb;">LEVEL</th>
                    <th style="text-align: center; padding: 12px; border-bottom: 1px solid #e5e7eb; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;">
                    <td style="text-align: center; padding: 16px 12px;">
                        <input type="checkbox" name="ids[]" value="{{ $item->user_id }}" class="row-checkbox" style="cursor: pointer; width: 16px; height: 16px; border-radius: 4px; border-color: #d1d5db;">
                    </td>
                    <td style="padding: 16px 12px; color: #374151;">{{ $loop->iteration }}</td>
                    <td style="padding: 16px 12px; color: #111827; font-weight: 500;">{{ $item->nama }}</td>
                    <td style="padding: 16px 12px; color: #4b5563;">{{ $item->email }}</td>
                    <td style="padding: 16px 12px; color: #4b5563;">{{ $item->level }}</td>
                    <td style="text-align: center; padding: 16px 12px; display: flex; justify-content: center; gap: 8px;">

                        <!-- Tombol Edit (Ikon Bulat Abu-abu) -->
                        <button type="button" title="Edit Mahasiswa"
                            style="background: transparent; border: 1px solid #d1d5db; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #4b5563; transition: all 0.2s;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"
                            data-item="{{ json_encode($item) }}"
                            onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>

                        <!-- Tombol Hapus (Ikon Bulat Merah) -->
                        <button type="button" title="Hapus Mahasiswa"
                            style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #ef4444; transition: all 0.2s;"
                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                            onclick="confirmSingleDelete('{{ route('admin.mahasiswa.destroy', $item->user_id) }}', '{{$item->nama }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<!-- Modal Form Mahasiswa -->
<div id="mahasiswaModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Mahasiswa</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="mahasiswaForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="kelas_id" value="{{ $kelas->kelas_id }}">

            <label class="field-group">
                <span>Nama</span>
                <input type="text" name="nama" id="inputNama" required>
            </label>

            <label class="field-group">
                <span>Email</span>
                <input type="email" name="email" id="inputEmail" required>
            </label>

            <label class="field-group">
                <span>Password</span>
                <input type="password" name="password" id="inputPassword" placeholder="Kosongkan jika tidak ingin mengubah">
            </label>

            <div class="action-cell" style="margin-top: 20px;">
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
                Apakah Anda yakin ingin menghapus mahasiswa <strong id="singleDeleteTitleText" style="color: #c62828;"></strong>?<br>Tindakan ini tidak dapat dibatalkan.
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
                Apakah Anda yakin ingin menghapus <strong id="bulkDeleteCountText" style="color: #c62828;">0</strong> mahasiswa yang dipilih?<br>Tindakan ini tidak dapat dibatalkan.
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

    /* PENYESUAIAN DESAIN DATATABLES SESUAI REFERENSI GAMBAR */
    .dataTables_wrapper {
        position: relative;
        clear: both;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 20px;
        color: #4b5563;
        font-size: 14px;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db;
        padding: 6px 12px;
        border-radius: 6px;
        margin: 0 5px;
        outline: none;
        background: #fff;
    }

    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 20px;
        color: #4b5563;
        font-size: 14px;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        border-radius: 6px;
        outline: none;
        margin-left: 8px;
        font-size: 14px;
        min-width: 250px;
    }

    .dataTables_wrapper .dataTables_info {
        float: left;
        padding-top: 20px;
        color: #4b5563;
        font-size: 14px;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right;
        padding-top: 15px;
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 14px;
        border: 1px solid transparent;
        border-radius: 6px;
        cursor: pointer;
        color: #4b5563 !important;
        background: transparent;
        font-size: 14px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        border: 1px solid #d1d5db;
        font-weight: bold;
        background: #fff;
        color: #111827 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .dataTables_wrapper::after {
        content: "";
        display: table;
        clear: both;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }

    /* Mematikan icon sorting DataTables default agar tidak bertabrakan dengan panah */
    table.dataTable thead th,
    table.dataTable thead td {
        border-bottom: none;
    }
</style>

<script>
    let dataTable;

    $(document).ready(function() {
        dataTable = $('#tableMahasiswa').DataTable({
            "language": {
                "search": "Cari Cepat:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": ">>",
                    "previous": "<<"
                },
                "zeroRecords": "Tidak ada data mahasiswa yang ditemukan",
                "infoEmpty": "Tidak ada data yang tersedia"
            },
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 5]
            }],
            "pageLength": 10,
            "dom": '<"top"lf>rt<"bottom"ip><"clear">' // Struktur peletakan elemen DataTables
        });

        $('#selectAll').on('change', function() {
            let isChecked = this.checked;
            dataTable.$('.row-checkbox').prop('checked', isChecked);
            updateBulkDeleteButton();
        });

        $('#tableMahasiswa tbody').on('change', '.row-checkbox', function() {
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const checkedCount = dataTable.$('.row-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = checkedCount;

        document.getElementById('btnBulkDelete').style.display = (checkedCount > 0) ? 'inline-block' : 'none';

        const totalCheckboxes = dataTable.$('.row-checkbox').length;
        document.getElementById('selectAll').checked = (checkedCount === totalCheckboxes && totalCheckboxes > 0);
    }

    function submitBulkDelete(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghapus...';
        button.disabled = true;
        button.previousElementSibling.disabled = true;

        const form = $('#bulkDeleteForm');
        form.find('.dt-hidden-id').remove();

        dataTable.$('.row-checkbox:checked').each(function() {
            form.append('<input type="hidden" name="ids[]" class="dt-hidden-id" value="' + $(this).val() + '">');
        });

        form.submit();
    }

    setTimeout(function() {
        const alerts = document.querySelectorAll('.auto-close-alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 10000);

    const modal = document.getElementById('mahasiswaModal');
    const form = document.getElementById('mahasiswaForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const storeUrl = "{{ route('admin.mahasiswa.store') }}";
    const updateUrlBase = "{{ url('mahasiswa') }}";

    function openModal(mode, data = null) {
        form.reset();
        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Mahasiswa Baru';
            form.action = storeUrl;
            formMethod.value = 'POST';
            document.getElementById('inputPassword').required = true;
            document.getElementById('inputPassword').placeholder = "Masukkan password";
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Mahasiswa';
            form.action = `${updateUrlBase}/${data.user_id}`;
            formMethod.value = 'PUT';
            document.getElementById('inputPassword').required = false;
            document.getElementById('inputPassword').placeholder = "Kosongkan jika tidak diubah";
            document.getElementById('inputNama').value = data.nama || '';
            document.getElementById('inputEmail').value = data.email || '';
        }
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

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

    const bulkModal = document.getElementById('bulkDeleteModal');
    const bulkDeleteCountText = document.getElementById('bulkDeleteCountText');

    function confirmBulkDelete() {
        bulkDeleteCountText.innerText = document.getElementById('selectedCount').innerText;
        bulkModal.style.display = 'block';
    }

    function closeBulkDeleteModal() {
        bulkModal.style.display = 'none';
    }

    const singleDeleteModal = document.getElementById('singleDeleteModal');
    const singleDeleteForm = document.getElementById('singleDeleteForm');
    const singleDeleteTitleText = document.getElementById('singleDeleteTitleText');

    function confirmSingleDelete(url, namaMahasiswa) {
        singleDeleteForm.action = url;
        singleDeleteTitleText.innerText = namaMahasiswa;
        singleDeleteModal.style.display = 'block';
    }

    function closeSingleDeleteModal() {
        singleDeleteModal.style.display = 'none';
    }

    function submitSingleDelete(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
        button.disabled = true;
        button.previousElementSibling.disabled = true;
        singleDeleteForm.submit();
    }

    window.onclick = function(event) {
        if (event.target === modal) closeModal();
        if (event.target === bulkModal) closeBulkDeleteModal();
        if (event.target === singleDeleteModal) closeSingleDeleteModal();
    }
</script>
@endsection