@extends('layouts.admin')

@section('title', 'Mahasiswa')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Data</p>
        <h1>Kelola Mahasiswa</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Mahasiswa</button>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Mahasiswa</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Kelas</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->kelas?->nama_kelas ?? '-' }}</td>
                <td>{{ $item->level }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan parameter data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.mahasiswa.destroy', $item->user_id) }}" 
                            data-delete-title="Hapus Mahasiswa">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state">Belum ada mahasiswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
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
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

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

<!-- CSS Modal Overlay -->
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: flex; align-items: flex-start; justify-content: center;
        overflow-y: auto; padding: 20px;
    }
</style>

<!-- JavaScript untuk handle Modal -->
<script>
    const modal = document.getElementById('mahasiswaModal');
    const form = document.getElementById('mahasiswaForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.mahasiswa.store') }}";
    const updateUrlBase = "{{ url('admin/mahasiswa') }}"; // Base URL untuk update tanpa ID

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka (termasuk mengosongkan input password)
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Mahasiswa';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk insert
            document.getElementById('inputPassword').required = true; // Wajib diisi saat tambah baru
            document.getElementById('inputPassword').placeholder = "Masukkan password";
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Mahasiswa';
            // Gabungkan Base URL dengan ID User
            form.action = `${updateUrlBase}/${data.user_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk update
            
            // Hapus atribut required pada password saat edit
            document.getElementById('inputPassword').required = false; 
            document.getElementById('inputPassword').placeholder = "Kosongkan jika tidak ingin mengubah";
            
            // Isi form dengan data yang dilempar dari button Edit
            document.getElementById('inputNama').value = data.nama || '';
            document.getElementById('inputEmail').value = data.email || '';
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