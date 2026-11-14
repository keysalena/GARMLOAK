@extends('layouts.admin')

@section('title', 'AR Content')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Konten</p>
        <h1>{{ $selectedModule ? 'AR Content - ' . $selectedModule->judul : 'Kelola AR Content' }}</h1>
    </div>
    @if (!$selectedModule || $items->isEmpty())
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah AR</button>
    @else
        <span class="pill neutral">AR untuk modul ini sudah tersedia</span>
    @endif
</div>

@if ($errors->any())
    <div role="alert" style="padding: 16px; background-color: #fff3f3; color: #b42318; border: 1px solid #fecdca; border-radius: 8px; margin-bottom: 20px;">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (session('success'))
    <div role="status" style="padding: 16px; background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="panel-card">
    <div class="panel-header">
        <h3>{{ $selectedModule ? 'AR Content Modul' : 'Daftar AR Content' }}</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Modul</th>
                <th>Judul</th>
                <th>Link Web AR</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->module?->judul ?? '-' }}</td>
                <td>{{ $item->judul }}</td>
                <td>
                    @if($item->model_3d_url)
                        <a href="{{ $item->model_3d_url }}" target="_blank" style="color: #0d6efd; text-decoration: underline; font-weight: 500;">
                            Lihat AR &nearr;
                        </a>
                    @else
                        <span style="color: #6c757d;">Belum ada link</span>
                    @endif
                </td>
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
                <td colspan="5" class="empty-state">Belum ada AR content.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Form AR Content -->
<div id="arModal" class="modal-overlay" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 10vh auto; position: relative;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah AR Content</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="arForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Modul</span>
                <select name="module_id" id="inputModule" required>
                    <option value="" disabled selected>Pilih Modul</option>
                    @foreach ($modules as $module)
                    <option value="{{ $module->module_id }}"
                            data-has-ar="{{ ($arContentCounts[$module->module_id] ?? 0) > 0 ? 'true' : 'false' }}">
                        {{ $module->judul }}{{ ($arContentCounts[$module->module_id] ?? 0) > 0 ? ' (sudah ada AR)' : '' }}
                    </option>
                    @endforeach
                </select>
            </label>

            <label class="field-group">
                <span>Judul AR</span>
                <input type="text" name="judul" id="inputJudul" placeholder="Contoh: AR Anatomi CPU" required>
            </label>

            <label class="field-group">
                <span>Link Web AR</span>
                <input type="url" name="model_3d_url" id="inputLink" placeholder="https://..." required>
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
    const selectedModuleId = @json($selectedModule?->module_id);
    const moduleSelect = document.getElementById('inputModule');

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah AR Content';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk Tambah
            moduleSelect.value = selectedModuleId || '';
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit AR Content';
            form.action = `${updateUrlBase}/${data.ar_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk Edit
            
            // Isi form dengan data yang dilempar
            moduleSelect.value = data.module_id || '';
            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputLink').value = data.model_3d_url || '';
        }

        Array.from(moduleSelect.options).forEach(option => {
            option.disabled = option.dataset.hasAr === 'true'
                && (mode === 'create' || option.value !== String(data?.module_id));
        });
        
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
        // Validasi native HTML5 sebelum animasi loading berjalan
        if(!form.checkValidity()) {
            return;
        }
        
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
        setTimeout(() => {
            button.disabled = true;
        }, 10);
    }
</script>
@endsection