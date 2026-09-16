@extends('layouts.admin')

@section('title', 'Challenge')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Latihan</p>
        <h1>Kelola Challenge</h1>
    </div>
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
                    <a href="{{ route('admin.challenge.questions', $item->challenge_id) }}" class="btn btn-small btn-primary">Kelola Soal</a>

                    <button type="button" class="btn btn-small btn-secondary" 
                        data-item="{{ json_encode($item) }}"
                        onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">Edit</button>
                    
                    <button type="button" class="btn btn-small btn-danger" 
                        onclick="deleteChallenge('{{ route('admin.challenge.destroy', $item->challenge_id) }}')">Hapus</button>
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

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<div id="challengeModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Challenge</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="challengeForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Judul</span>
                <input type="text" name="judul" id="inputJudul" required>
            </label>

            <label class="field-group">
                <span>Modul</span>
                <select name="module_id" id="inputModule" required>
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

<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1000;
        display: flex; align-items: flex-start; justify-content: center;
        overflow-y: auto; padding: 20px;
    }
</style>

<script>
    const modal = document.getElementById('challengeModal');
    const form = document.getElementById('challengeForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    const storeUrl = "{{ route('admin.challenge.store') }}";
    const updateUrlBase = "{{ url('challenge') }}";

    function openModal(mode, data = null) {
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Challenge';
            form.action = storeUrl;
            formMethod.value = 'POST';
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Challenge';
            form.action = `${updateUrlBase}/${data.challenge_id}`;
            formMethod.value = 'PUT';
            
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputModule').value = data.module_id || '';
        }
        
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function deleteChallenge(url) {
        if (confirm('Apakah Anda yakin ingin menghapus Challenge ini beserta semua soal di dalamnya?')) {
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