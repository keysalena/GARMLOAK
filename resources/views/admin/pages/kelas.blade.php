@extends('layouts.admin')

@section('title', 'Kelas')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Data</p>
        <h1>Kelola Kelas</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
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
                <th>Tahun Ajaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->kelas_id }}</td>
                <td>{{ $item->nama_kelas }}</td>
                <td>{{ $item->tahun_ajaran ?? '-' }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan parameter data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.kelas.destroy', $item->kelas_id) }}" 
                            data-delete-title="Hapus Kelas">Hapus</button>
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

<!-- Modal Form Kelas -->
<div id="kelasModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Kelas</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="kelasForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Nama Kelas</span>
                <input type="text" name="nama_kelas" id="inputNamaKelas" required>
            </label>

            <label class="field-group">
                <span>Tahun Ajaran</span>
                <input type="text" name="tahun_ajaran" id="inputTahunAjaran">
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
    const modal = document.getElementById('kelasModal');
    const form = document.getElementById('kelasForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.kelas.store') }}";
    const updateUrlBase = "{{ url('admin/kelas') }}"; // Base URL untuk update tanpa ID

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Kelas';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk insert
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Kelas';
            // Gabungkan Base URL dengan ID Kelas
            form.action = `${updateUrlBase}/${data.kelas_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk update
            
            // Isi form dengan data yang dilempar dari button Edit
            document.getElementById('inputNamaKelas').value = data.nama_kelas || '';
            document.getElementById('inputTahunAjaran').value = data.tahun_ajaran || '';
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