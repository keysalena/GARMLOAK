@extends('layouts.admin')

@section('title', 'Soal Kuis Modul')

@section('breadcrumb')
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li>
    <a href="{{ route('admin.modul.index') }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Kelola Modul</a>
</li>
<li style="color: #adb5bd; font-size: 12px;">/</li>
<li style="color: #2e7d32; font-weight: 600;" aria-current="page">Kelola Kuis</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <!-- Menampilkan data dari $module -->
        <p class="eyebrow text-green">Modul: {{ $module->kode_modul }}</p>
        <h1>Soal Kuis: {{ $module->judul }}</h1>
    </div>
    <div>
        <!-- Tombol kembali diarahkan ke index modul -->
        <a href="{{ route('admin.modul.index') }}" class="btn btn-secondary">Kembali ke Modul</a>
        <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Soal</button>
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
        <h3>Daftar Pertanyaan</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>XP</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ \Illuminate\Support\Str::limit($item->pertanyaan, 70) }}</td>
                <td>{{ $item->xp_reward }}</td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <!-- Ubah bagian ini di questions.blade.php -->
                <td class="action-cell">
                    <a href="{{ route('admin.questions.options', $item->question_id) }}" class="btn btn-small btn-primary">Opsi Jawaban</a>

                    <!-- Tombol Edit (Ikon Bulat Abu-abu) -->
                    <button type="button" title="Edit Pertanyaan"
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
                    <button type="button" title="Hapus Pertanyaan"
                        style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 50%; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #ef4444; transition: all 0.2s;"
                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                        onclick="confirmSingleDelete('{{ route('admin.questions.destroy', $item->question_id) }}', '{{$item->pertanyaan }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state">Belum ada pertanyaan.</td>
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
                Apakah Anda yakin ingin menghapus pertanyaan <strong id="singleDeleteTitleText" style="color: #c62828;"></strong>?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeSingleDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="submitSingleDelete(this)">Ya, Hapus</button>
        </div>
    </div>
</div>

<div id="questionModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Question</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="questionForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <!-- UBAH DARI challenge_id MENJADI module_id -->
            <input type="hidden" name="module_id" value="{{ $module->module_id }}">

            <label class="field-group">
                <span>Pertanyaan</span>
                <textarea name="pertanyaan" id="inputPertanyaan" rows="4" required></textarea>
            </label>

            <label class="field-group">
                <span>XP Reward</span>
                <input type="number" name="xp_reward" id="inputXpReward" required>
            </label>

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
    const modal = document.getElementById('questionModal');
    const form = document.getElementById('questionForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const storeUrl = "{{ route('admin.questions.store') }}";
    // Perbaikan URL Base (Pastikan mengarah ke /questions)
    const updateUrlBase = "{{ url('questions') }}";

    function openModal(mode, data = null) {
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Question';
            form.action = storeUrl;
            formMethod.value = 'POST';
            document.getElementById('inputXpReward').value = 30;
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Question';
            form.action = `${updateUrlBase}/${data.question_id}`;
            formMethod.value = 'PUT';

            document.getElementById('inputPertanyaan').value = data.pertanyaan || '';
            document.getElementById('inputXpReward').value = data.xp_reward || 30;
        }

        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
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