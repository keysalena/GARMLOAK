@extends('layouts.admin')

@section('title', 'Challenge')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Latihan</p>
        <h1>Kelola Challenge</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Challenge</button>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Challenge</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Modul</th>
                <th>Jumlah Soal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->module?->judul ?? '-' }}</td>
                <td>{{ $item->jumlah_soal }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.challenge.destroy', $item->challenge_id) }}" 
                            data-delete-title="Hapus Challenge">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada challenge.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Form Challenge -->
<div id="challengeModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Challenge</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="challengeForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Judul</span>
                <input type="text" name="judul" id="inputJudul" required>
            </label>

            <label class="field-group">
                <span>Modul</span>
                <select name="module_id" id="inputModule">
                    <option value="" disabled selected>Pilih Modul</option>
                    @foreach ($modules as $module)
                    <option value="{{ $module->module_id }}">{{ $module->judul }}</option>
                    @endforeach
                </select>
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
    const modal = document.getElementById('challengeModal');
    const form = document.getElementById('challengeForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.challenge.store') }}";
    const updateUrlBase = "{{ url('admin/challenge') }}"; // Base URL untuk update tanpa ID

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Challenge';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk Tambah
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Challenge';
            // Gabungkan Base URL dengan ID Challenge
            form.action = `${updateUrlBase}/${data.challenge_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk Edit
            
            // Isi form dengan data yang dilempar
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputModule').value = data.module_id || '';
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