@extends('layouts.admin')

@section('title', 'Kelas')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Data</p>
        <h1>Kelola Kelas</h1>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Kelas</button>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Kelas</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kelas</th>
                <th>Angkatan</th>
                <th>Jumlah Mahasiswa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->kelas_id }}</td>
                <td>{{ $item->nama_kelas }}</td>
                <td>{{ $item->tahun_ajaran ?? '-' }}</td>
                <td>{{ $item->mahasiswa_count }} Orang</td>
                <td class="action-cell">
                    <!-- Tombol Kelola Mahasiswa -->
                    <a href="{{ route('admin.kelas.mahasiswa', $item->kelas_id) }}" class="btn btn-small btn-primary">Kelola Mahasiswa</a>

                    <button type="button" class="btn btn-small btn-secondary"
                        data-item="{{ json_encode($item) }}"
                        onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">Edit</button>

                    <button type="button" class="btn btn-small btn-danger"
                        onclick="deleteKelas('{{ route('admin.kelas.destroy', $item->kelas_id) }}')">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada data kelas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Form Hapus -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
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
    const modal = document.getElementById('kelasModal');
    const form = document.getElementById('kelasForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const storeUrl = "{{ route('admin.kelas.store') }}";
    const updateUrlBase = "{{ url('kelas') }}"; // Sesuaikan base URL

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

    function deleteKelas(url) {
        if (confirm('Yakin ingin menghapus Kelas ini? Seluruh mahasiswa di dalamnya mungkin akan terdampak.')) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = url;
            deleteForm.submit();
        }
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection