@extends('layouts.admin')

@section('title', 'Materi Modul')

@section('content')
<script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>

<div class="page-header">
    <div>
        <p class="eyebrow text-green">Modul: {{ $module->kode_modul }}</p>
        <h1>Materi: {{ $module->judul }}</h1>
    </div>
    <div>
        <a href="{{ route('admin.modul.index') }}" class="btn btn-secondary">Kembali ke Modul</a>
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Materi</button>
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Materi</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->judul }}</td>
                <td>
                    <span class="badge {{ $item->tipe === 'pendahuluan' ? 'bg-warning text-dark' : $item->tipe }}">
                        {{ $item->tipe === 'pendahuluan' ? 'Pendahuluan' : ucfirst($item->tipe) }}
                    </span>
                </td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <td class="action-cell">
                    <!-- PERBAIKAN: Gunakan {{ $item }} langsung agar data HTML aman di-parse JSON -->
                    <button type="button" class="btn btn-small btn-secondary"
                        data-item="{{ $item }}"
                        onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">
                        Edit
                    </button>
                    <button type="button" class="btn btn-small btn-danger"
                        onclick="deleteMateri('{{ route('admin.materi.destroy', $item->materi_id) }}')">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada materi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- MODAL FORM -->
<div id="materiModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 900px; margin: 3% auto; width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Materi Baru</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <!-- PERBAIKAN: Pindahkan syncEditor ke onsubmit form -->
        <form id="materiForm" method="POST" action="" enctype="multipart/form-data" class="login-form" style="padding: 20px;" onsubmit="syncEditor()">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="module_id" id="inputModule" value="{{ $module->module_id }}">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label class="field-group">
                    <span>Judul Materi / Pendahuluan</span>
                    <input type="text" name="judul" id="inputJudul" required>
                </label>

                <label class="field-group">
                    <span>Tipe Konten</span>
                    <select name="tipe" id="inputTipe" required>
                        <option value="teks">Teks (Artikel Lengkap)</option>
                        <option value="pendahuluan">Pendahuluan (Teks Awal)</option>
                        <option value="video">Video</option>
                        <option value="pdf">Dokumen PDF</option>
                    </select>
                </label>

                <label class="field-group" style="grid-column: 1 / -1;">
                    <span>Urutan Tampil</span>
                    <input type="number" name="urutan" id="inputUrutan" min="1" required style="width: 50%;">
                </label>
            </div>

            <hr style="margin: 20px 0; border: 1px solid #eee;">

            <div id="containerTeks" class="field-group">
                <span id="labelKontenTeks" style="font-weight: bold; margin-bottom: 8px; display: block;">Isi Materi (Teks)</span>
                <textarea name="konten_teks" id="inputKontenTeks" rows="10"></textarea>
            </div>

            <div id="containerUrl" class="field-group" style="display: none;">
                <span>Link URL (YouTube / Google Drive)</span>
                <input type="url" name="konten_url" id="inputKontenUrl" placeholder="Contoh: https://www.youtube.com/watch?v=xxx">
            </div>

            <div id="containerFile" class="field-group" style="display: none; margin-top: 15px;">
                <span>ATAU Upload File (Opsional)</span>
                <input type="file" name="konten_file" id="inputKontenFile" accept=".pdf,video/mp4">
            </div>

            <div class="action-cell" style="margin-top: 25px; text-align: right;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Simpan Materi</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        overflow-y: auto;
        padding: 20px;
    }
</style>

<script>
    CKEDITOR.replace('inputKontenTeks', {
        height: 300,
        toolbar: [
            { name: 'document', items: ['Source'] },
            { name: 'clipboard', items: ['Undo', 'Redo'] },
            { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] }
        ],
        removeButtons: 'Subscript,Superscript'
    });

    const modal = document.getElementById('materiModal');
    const form = document.getElementById('materiForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const inputTipe = document.getElementById('inputTipe');
    const containerTeks = document.getElementById('containerTeks');
    const labelKontenTeks = document.getElementById('labelKontenTeks');
    const containerUrl = document.getElementById('containerUrl');
    const containerFile = document.getElementById('containerFile');

    const storeUrl = "{{ route('admin.materi.store') }}";
    const updateUrlBase = "{{ url('materi') }}";

    inputTipe.addEventListener('change', function() {
        toggleKontenInput(this.value);
    });

    function toggleKontenInput(tipe) {
        containerTeks.style.display = 'none';
        containerUrl.style.display = 'none';
        containerFile.style.display = 'none';

        if (tipe === 'teks' || tipe === 'pendahuluan') {
            containerTeks.style.display = 'block';
            labelKontenTeks.innerText = (tipe === 'pendahuluan') ? "Isi Pendahuluan Singkat (Akan muncul paling awal)" : "Isi Materi (Teks Lengkap)";
        } else if (tipe === 'video' || tipe === 'pdf') {
            containerUrl.style.display = 'block';
            containerFile.style.display = 'block';
            document.getElementById('inputKontenFile').accept = tipe === 'pdf' ? '.pdf' : 'video/*';
        }
    }

    function openModal(mode, data = null) {
        form.reset();
        
        // Coba reset CKEditor dengan aman
        try { CKEDITOR.instances.inputKontenTeks.setData(''); } catch(e) {}

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Materi Baru';
            form.action = storeUrl;
            formMethod.value = 'POST';
            toggleKontenInput('teks');
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Materi';
            form.action = `${updateUrlBase}/${data.materi_id}`;
            formMethod.value = 'PUT';

            document.getElementById('inputJudul').value = data.judul || '';
            document.getElementById('inputModule').value = data.module_id || '{{ $module->module_id }}';
            document.getElementById('inputUrutan').value = data.urutan || '';
            document.getElementById('inputTipe').value = data.tipe || 'teks';

            toggleKontenInput(data.tipe || 'teks');

            if (data.tipe === 'teks' || data.tipe === 'pendahuluan') {
                // PERBAIKAN: Gunakan setTimeout agar CKEditor punya waktu untuk me-load data
                setTimeout(() => {
                    CKEDITOR.instances.inputKontenTeks.setData(data.konten_teks || '');
                }, 100);
            } else {
                document.getElementById('inputKontenUrl').value = data.konten_url || '';
            }
        }
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function syncEditor() {
        // Ambil data HTML dari CKEditor dan paksakan masuk ke textarea sebelum form disubmit
        const data = CKEDITOR.instances.inputKontenTeks.getData();
        document.getElementById('inputKontenTeks').value = data;
    }

    function deleteMateri(url) {
        if (confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
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