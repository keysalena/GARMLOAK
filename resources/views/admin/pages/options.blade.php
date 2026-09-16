@extends('layouts.admin')

@section('title', 'Opsi Jawaban')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Soal Modul: {{ $question->module->judul ?? 'Modul' }}</p>
        <h1>Opsi untuk: "{{ \Illuminate\Support\Str::limit($question->pertanyaan, 50) }}"</h1>
    </div>
    <div>
        <!-- Kembali ke daftar soal pada modul tersebut -->
        <a href="{{ route('admin.modul.questions', $question->module_id) }}" class="btn btn-secondary">Kembali ke Soal</a>

        <!-- Sembunyikan tombol tambah jika opsi sudah 4 (atau biarkan jika ingin fleksibel) -->
        @if($items->count() < 4)
            <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Opsi</button>
            @endif
    </div>
</div>

<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Opsi Jawaban</h3>
        <span class="pill neutral">{{ $items->count() }} / 4 Opsi</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Teks Opsi</th>
                <th>Status Jawaban</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ $item->teks_opsi }}</td>
                <td>
                    @if($item->is_correct)
                    <span class="badge text-green" style="background: #e8f5e9; padding: 5px 10px; border-radius: 4px;">✔ Jawaban Benar</span>
                    @else
                    <span class="badge text-gray">Salah</span>
                    @endif
                </td>
                <td class="action-cell">
                    <button type="button" class="btn btn-small btn-secondary"
                        data-item="{{ json_encode($item) }}"
                        onclick="openModal('edit', JSON.parse(this.getAttribute('data-item')))">Edit</button>

                    <button type="button" class="btn btn-small btn-danger"
                        onclick="deleteOption('{{ route('admin.options.destroy', $item->option_id) }}')">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="empty-state">Belum ada opsi jawaban. Tambahkan minimal 4 opsi (A,B,C,D).</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Form -->
<div id="optionModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Opsi</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="optionForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="question_id" value="{{ $question->question_id }}">

            <label class="field-group">
                <span>Teks Pilihan Jawaban</span>
                <input type="text" name="teks_opsi" id="inputTeksOpsi" required>
            </label>

            <label style="display: flex; align-items: center; gap: 10px; margin-top: 15px; cursor: pointer;">
                <input type="checkbox" name="is_correct" id="inputIsCorrect" value="1" style="width: 20px; height: 20px;">
                <span style="font-weight: bold; color: #2e7d32;">Tandai sebagai Jawaban Benar</span>
            </label>
            <small style="color: gray; display: block; margin-top: 5px;">*Jika dicentang, opsi benar sebelumnya akan otomatis menjadi salah.</small>

            <div class="action-cell" style="margin-top: 25px;">
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
    const modal = document.getElementById('optionModal');
    const form = document.getElementById('optionForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    const storeUrl = "{{ route('admin.options.store') }}";
    const updateUrlBase = "{{ url('options') }}";

    function openModal(mode, data = null) {
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Opsi Jawaban';
            form.action = storeUrl;
            formMethod.value = 'POST';
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Opsi Jawaban';
            form.action = `${updateUrlBase}/${data.option_id}`;
            formMethod.value = 'PUT';

            document.getElementById('inputTeksOpsi').value = data.teks_opsi || '';
            document.getElementById('inputIsCorrect').checked = data.is_correct == 1;
        }

        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function deleteOption(url) {
        if (confirm('Apakah Anda yakin ingin menghapus opsi jawaban ini?')) {
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