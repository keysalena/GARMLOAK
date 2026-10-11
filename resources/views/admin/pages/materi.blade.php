@extends('layouts.admin')

@section('title', 'Materi Modul')
@section('breadcrumb')
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li>
    <a href="{{ route('admin.modul.index') }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Kelola Modul</a>
</li>
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li style="color: #2e7d32; font-weight: 600;" aria-current="page">Kelola Materi</li>
@endsection
@section('content')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/48.5.2/ckeditor5.css">
<script src="https://cdn.ckeditor.com/ckeditor5/48.5.2/ckeditor5.umd.js"></script>

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
@if (session('success'))
<div style="padding: 16px; background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
    <div>
        <strong>✔ Berhasil!</strong> {{ session('success') }}
    </div>
    <button type="button" onclick="this.parentElement.style.display='none';" style="background: transparent; border: none; font-size: 1.2rem; color: #2e7d32; cursor: pointer;">&times;</button>
</div>
@endif
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Materi</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>NO</th>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->judul }}</td>
                <td>
                    <span class="badge {{ $item->tipe === 'pendahuluan' ? 'bg-warning text-dark' : $item->tipe }}">
                        {{ $item->tipe === 'pendahuluan' ? 'Pendahuluan' : ucfirst($item->tipe) }}
                    </span>
                </td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit (Ikon Bulat Abu-abu) -->
                    <button type="button" title="Edit Materi"
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
                    <button type="button" title="Hapus Materi"
                        style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #ef4444; transition: all 0.2s;"
                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                        onclick="confirmSingleDelete('{{ route('admin.materi.destroy', $item->materi_id) }}', '{{$item->judul }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
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
                Apakah Anda yakin ingin menghapus materi <strong id="singleDeleteTitleText" style="color: #c62828;"></strong>?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeSingleDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="submitSingleDelete(this)">Ya, Hapus</button>
        </div>
    </div>
</div>

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

            <div class="form-grid-2">
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

            <div class="action-cell" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Simpan</button>
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
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        overflow-y: auto;
        padding: 20px;
    }

    .ck.ck-balloon-panel {
        z-index: 10010 !important;
    }

    .ck-editor__editable_inline {
        min-height: 300px;
    }
</style>

<script>
    let editorInstance = null;

    const {
        ClassicEditor,
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        RemoveFormat,
        Font,
        Alignment,
        List,
        Indent,
        BlockQuote,
        Link,
        AutoLink,
        PasteFromOffice,
        Table,
        TableToolbar,
        HorizontalLine,
        SourceEditing,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize,
        ImageUpload,
        SimpleUploadAdapter
    } = CKEDITOR;

    ClassicEditor.create(document.querySelector('#inputKontenTeks'), {
        licenseKey: 'GPL',
        plugins: [
            Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough, RemoveFormat,
            Font, Alignment, List, Indent, BlockQuote, Link, AutoLink, PasteFromOffice,
            Table, TableToolbar, HorizontalLine, SourceEditing,
            Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageUpload, SimpleUploadAdapter
        ],
        toolbar: {
            items: [
                'sourceEditing', '|', 'undo', 'redo', '|',
                'heading', 'fontFamily', 'fontSize', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'removeFormat', '|',
                'fontColor', 'fontBackgroundColor', '|',
                'numberedList', 'bulletedList', 'outdent', 'indent', 'blockQuote', 'alignment', '|',
                'link', 'uploadImage', 'insertTable', 'horizontalLine'
            ],
            shouldNotGroupWhenFull: true
        },
        simpleUpload: {
            uploadUrl: "{{ route('admin.upload.image') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        },
        image: {
            toolbar: [
                'toggleImageCaption', 'imageTextAlternative', '|',
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|', 'resizeImage'
            ]
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        }
    })
    .then(editor => {
        editorInstance = editor;
    })
    .catch(error => console.error(error));

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

        if (editorInstance) editorInstance.setData('');

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
                if (editorInstance) editorInstance.setData(data.konten_teks || '');
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
        if (editorInstance) {
            document.getElementById('inputKontenTeks').value = editorInstance.getData();
        }
    }

    function setLoading(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        setTimeout(() => {
            button.disabled = true;
        }, 10);
    }
    const singleDeleteModal = document.getElementById('singleDeleteModal');
    const singleDeleteForm = document.getElementById('singleDeleteForm');
    const singleDeleteTitleText = document.getElementById('singleDeleteTitleText');

    function confirmSingleDelete(url, namaKelas) {
        // Set URL tujuan form hapus
        singleDeleteForm.action = url;
        // Tampilkan nama kelas di teks modal
        singleDeleteTitleText.innerText = namaKelas;
        // Buka modal
        singleDeleteModal.style.display = 'block';
    }

    function closeSingleDeleteModal() {
        singleDeleteModal.style.display = 'none';
    }

    function submitSingleDelete(button) {
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
        button.disabled = true;
        button.previousElementSibling.disabled = true; // Nonaktifkan tombol Batal
        singleDeleteForm.submit();
    }
    // --- TUTUP MODAL JIKA KLIK AREA LUAR ---
    window.onclick = function(event) {
        if (event.target === modal) closeModal();
        if (event.target === singleDeleteModal) closeSingleDeleteModal();
    }
</script>
@endsection