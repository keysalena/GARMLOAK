@extends('layouts.admin')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Kelas: {{ $kelas->nama_kelas }} ({{ $kelas->tahun_ajaran }})</p>
        <h1>Daftar Mahasiswa</h1>
    </div>
    <div>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Kembali ke Kelas</a>
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Mahasiswa</button>
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Mahasiswa Terdaftar</h3>
        <span class="pill neutral">{{ $items->count() }} Orang</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->level }}</td>
                <td class="action-cell">
                    <button type="button" class="btn btn-small btn-secondary" 
                            data-item="{{ json_encode($item) }}"
                            onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">Edit</button>
                    
                    <button type="button" class="btn btn-small btn-danger" 
                            onclick="deleteMahasiswa('{{ route('admin.mahasiswa.destroy', $item->user_id) }}')">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada mahasiswa di kelas ini.</td>
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
            
            <!-- Hidden input untuk menyambungkan dengan Kelas -->
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
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1000;
        display: flex; align-items: flex-start; justify-content: center;
        overflow-y: auto; padding: 20px;
    }
</style>

<script>
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

    function deleteMahasiswa(url) {
        if (confirm('Yakin ingin menghapus data Mahasiswa ini?')) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = url;
            deleteForm.submit();
        }
    }

    window.onclick = function(event) {
        if (event.target === modal) { closeModal(); }
    }
</script>
@endsection