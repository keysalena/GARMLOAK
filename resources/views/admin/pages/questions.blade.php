@extends('layouts.admin')

@section('title', 'Soal Kuis Modul')

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

                    <button type="button" class="btn btn-small btn-secondary"
                        data-item="{{ json_encode($item) }}"
                        onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">Edit</button>

                    <button type="button" class="btn btn-small btn-danger"
                        onclick="deleteQuestion('{{ route('admin.questions.destroy', $item->question_id) }}')">Hapus</button>
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

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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
                <button type="submit" class="btn btn-primary">Simpan</button>
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

    function deleteQuestion(url) {
        if (confirm('Apakah Anda yakin ingin menghapus Soal ini?')) {
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