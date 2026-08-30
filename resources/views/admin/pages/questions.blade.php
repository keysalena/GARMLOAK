@extends('layouts.admin')

@section('title', 'Questions')

@section('content')
<!-- ==========================================
     HEADER & TOMBOL TAMBAH
=========================================== -->
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Latihan</p>
        <h1>Kelola Questions</h1>
    </div>
    <!-- Tombol Add memanggil fungsi JS openModal('create') -->
    <button type="button" class="btn btn-primary" onclick="openModal('create')">+ Tambah Question</button>
</div>

<!-- ==========================================
     TABEL DATA QUESTIONS
=========================================== -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Daftar Pertanyaan</h3>
        <span class="pill neutral">{{ $items->count() }} item</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>Challenge</th>
                <th>XP</th>
                <th>Urutan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
            <tr>
                <td>{{ \Illuminate\Support\Str::limit($item->pertanyaan, 70) }}</td>
                <td>{{ $item->challenge?->judul ?? '-' }}</td>
                <td>{{ $item->xp_reward }}</td>
                <td>{{ $item->urutan ?? '-' }}</td>
                <td class="action-cell">
                    <!-- Tombol Edit memanggil fungsi JS dengan parameter data item -->
                    <button type="button" class="btn btn-small btn-secondary" 
                            onclick="openModal('edit', {{ $item }})">Edit</button>
                    
                    <!-- Tombol Hapus -->
                    <button type="button" class="btn btn-small btn-danger" 
                            data-delete-url="{{ route('admin.questions.destroy', $item->question_id) }}" 
                            data-delete-title="Hapus Question">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state">Belum ada pertanyaan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==========================================
     MODAL FORM (TAMBAH / EDIT)
=========================================== -->
<div id="questionModal" class="modal-overlay" style="display: none;">
    <div class="modal-content panel-card" style="max-width: 500px; margin: 5% auto;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle">Tambah Question</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        
        <form id="questionForm" method="POST" action="" class="login-form" style="padding: 20px;">
            @csrf
            <!-- Method spoofing untuk keperluan Edit (PUT) -->
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <label class="field-group">
                <span>Challenge</span>
                <select name="challenge_id" id="inputChallenge" required>
                    <option value="" disabled selected>Pilih Challenge</option>
                    @foreach ($challenges as $challenge)
                    <option value="{{ $challenge->challenge_id }}">{{ $challenge->judul }}</option>
                    @endforeach
                </select>
            </label>

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

<!-- ==========================================
     JAVASCRIPT UNTUK MODAL
=========================================== -->
<script>
    const modal = document.getElementById('questionModal');
    const form = document.getElementById('questionForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Route dasar dari Laravel
    const storeUrl = "{{ route('admin.questions.store') }}";
    const updateUrlBase = "{{ url('admin/questions') }}"; // Base URL untuk update

    function openModal(mode, data = null) {
        // Reset form setiap kali modal dibuka
        form.reset();

        if (mode === 'create') {
            modalTitle.innerText = 'Tambah Question';
            form.action = storeUrl;
            formMethod.value = 'POST'; // Gunakan POST untuk insert
            
            // Set default value untuk XP saat tambah
            document.getElementById('inputXpReward').value = 30;
        } else if (mode === 'edit' && data) {
            modalTitle.innerText = 'Edit Question';
            // Gabungkan Base URL dengan ID Question
            form.action = `${updateUrlBase}/${data.question_id}`;
            formMethod.value = 'PUT'; // Gunakan PUT untuk update
            
            // Isi form dengan data yang dilempar dari button Edit
            document.getElementById('inputChallenge').value = data.challenge_id || '';
            document.getElementById('inputPertanyaan').value = data.pertanyaan || '';
            document.getElementById('inputXpReward').value = data.xp_reward || 30;
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