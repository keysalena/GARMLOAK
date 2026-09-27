@extends('layouts.admin')

@section('title', 'Opsi Jawaban')

@section('breadcrumb')
    <li style="color: #adb5bd; font-size: 12px;">/</li>
    <li>
        <a href="{{ route('admin.modul.index') }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Kelola Modul</a>
    </li>
    <li style="color: #adb5bd; font-size: 12px;">/</li>
    <li>
        <a href="{{ route('admin.modul.questions', $question->module_id) }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Kelola Kuis</a>
    </li>
    <li style="color: #adb5bd; font-size: 12px;">/</li>
    <li style="color: #2e7d32; font-weight: 600;" aria-current="page">Opsi Jawaban</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow text-green">Soal Modul: {{ $question->module->judul ?? 'Modul' }}</p>
        <h1>Opsi untuk: "{{ \Illuminate\Support\Str::limit($question->pertanyaan, 50) }}"</h1>
    </div>
    <div>
        <a href="{{ route('admin.modul.questions', $question->module_id) }}" class="btn btn-secondary">Kembali ke Soal</a>
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
        <h3>Atur Opsi Jawaban (A, B, C, D)</h3>
        <span class="pill neutral">Pilih salah satu radio button sebagai jawaban benar</span>
    </div>

    <!-- Pastikan action route mengarah ke controller storeBulkOptions yang baru dibuat -->
    <form method="POST" action="{{ route('admin.options.storeBulk') }}" style="padding: 20px;">
        @csrf
        <input type="hidden" name="question_id" value="{{ $question->question_id }}">

        {{-- Looping pasti 4 kali (A, B, C, D) --}}
        @for ($i = 0; $i < 4; $i++)
            @php
            // Ambil data opsi yang sudah ada dari database berdasarkan index (jika ada)
            $existing=$items->get($i);
            @endphp

            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">

                <!-- Hidden Input untuk menyimpan ID jika sedang Edit -->
                <input type="hidden" name="options[{{ $i }}][option_id]" value="{{ $existing ? $existing->option_id : '' }}">

                <!-- Huruf A, B, C, D (Menggunakan fungsi chr untuk konversi ASCII) -->
                <div style="font-weight: bold; font-size: 1.2rem; width: 30px; text-align: center;">
                    {{ chr(65 + $i) }}.
                </div>

                <!-- Input Teks Opsi -->
                <label class="field-group" style="flex: 1; margin: 0;">
                    <input type="text" name="options[{{ $i }}][teks_opsi]" value="{{ $existing ? $existing->teks_opsi : '' }}" placeholder="Masukkan teks opsi jawaban..." required>
                </label>

                <!-- Radio Button penentu Jawaban Benar -->
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; white-space: nowrap; padding: 10px; border-radius: 8px; background: {{ ($existing && $existing->is_correct) ? '#e8f5e9' : '#f9f9f9' }};">
                    <input type="radio" name="is_correct" value="{{ $i }}" {{ ($existing && $existing->is_correct) ? 'checked' : '' }} required style="width: 18px; height: 18px;">
                    <span class="text-green" style="font-weight: bold;">Jawaban Benar</span>
                </label>
            </div>
            @endfor

            <div class="action-cell" style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary" onclick="setLoading(this)">Simpan Semua Opsi</button>
            </div>
    </form>
</div>

<script>
    // Script loading untuk mencegah double submit (sesuai permintaan sebelumnya)
    function setLoading(button) {
        // Cek dulu apakah form sudah terisi (validasi required HTML5)
        const form = button.closest('form');
        if (form.checkValidity()) {
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            setTimeout(() => {
                button.disabled = true;
                form.submit(); // Submit form manual karena button didisable
            }, 10);
        } else {
            // Jika ada input yang kosong, biarkan browser menampilkan peringatan validasi
            form.reportValidity();
        }
    }
</script>
@endsection