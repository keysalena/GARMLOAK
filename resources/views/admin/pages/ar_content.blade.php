@extends('layouts.admin')

@section('title', 'AR Content')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Konten</p>
        <h1>Kelola AR Content</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah AR</button>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar AR Content</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Modul</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->module?->judul ?? '-' }}</td>
                <td>{{ $item->urutan }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.ar-content.destroy', $item->ar_id) }}" 
                            data-delete-title="Hapus AR Content">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada AR content.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Form AR Content -->
<div id="arModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah AR Content</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="arForm" method="POST" action="" class="login-form" style="padding: 20px;">
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

            <label class="field-group">
                <span>Penjelasan</span>
                <textarea name="penjelasan" id="inputPenjelasan" rows="4"></textarea>
            </label>

            <div class="action-cell" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('arModal');
    const form = document.getElementById('arForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.ar-content.store') }}";
    const updateUrlBase = "{{ url('ar-content') }}"; // URL base tanpa ID

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah AR Content';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk Tambah
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit AR Content';
            form.action = `${updateUrlBase}/${data.ar_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk Edit
            
            // Isi form dengan data yang dilempar
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputModule').value = data.module_id || '';
            document.getElementById('inputPenjelasan').value = data.penjelasan || '';
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
        function setLoading(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        setTimeout(() => {
            button.disabled = true;
        }, 10);
    }
</script>
@endsection