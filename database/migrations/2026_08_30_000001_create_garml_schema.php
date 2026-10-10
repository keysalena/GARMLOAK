<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->integer('level_number')->primary();
            $table->string('nama_level', 50)->nullable();
            $table->integer('xp_min');
            $table->integer('xp_max');
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id('kelas_id');
            $table->string('nama_kelas', 50);
            $table->string('tahun_ajaran', 9)->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id('module_id');
            $table->string('kode_modul', 10);
            $table->string('judul', 150);
            $table->string('sub_cpmk', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan');
            $table->string('icon_url', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('materi', function (Blueprint $table) {
            $table->id('materi_id');
            $table->foreignId('module_id')->constrained('modules', 'module_id')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->enum('tipe', ['teks', 'video', 'pdf', 'pendahuluan'])->default('teks');

            // Kolom baru untuk menyimpan isi materi berupa teks panjang (artikel/WYSIWYG)
            $table->longText('konten_teks')->nullable();

            // Kolom ini tetap digunakan untuk menyimpan link YouTube, Google Drive, atau path file PDF/Video
            $table->string('konten_url', 255)->nullable();

            $table->integer('urutan');
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('ar_content', function (Blueprint $table) {
            $table->id('ar_id');
            $table->foreignId('module_id')->constrained('modules', 'module_id')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->string('marker_image_url', 255)->nullable();
            $table->string('model_3d_url', 255)->nullable();
            $table->text('penjelasan')->nullable();
            $table->integer('urutan');
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id');
            // Langsung relasi ke modules
            $table->foreignId('module_id')->constrained('modules', 'module_id')->cascadeOnDelete(); 
            $table->text('pertanyaan');
            $table->text('penjelasan')->nullable();
            $table->integer('urutan');
            $table->integer('xp_reward')->default(30);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id('option_id');
            $table->foreignId('question_id')->constrained('questions', 'question_id')->cascadeOnDelete();
            $table->string('teks_opsi', 255);
            $table->boolean('is_correct')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
        
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas', 'kelas_id');
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('avatar_url', 255)->nullable();
            $table->integer('level')->default(1);
            $table->integer('total_xp')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('level')->references('level_number')->on('levels');
        });

        Schema::create('user_module_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules', 'module_id')->cascadeOnDelete();
            $table->tinyInteger('persen_selesai')->default(0);
            $table->enum('status', ['belum_mulai', 'berjalan', 'selesai'])->default('belum_mulai');
            $table->decimal('skor_rata_rata', 5, 2)->default(0);
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unique(['user_id', 'module_id']);
        });

        

        DB::table('levels')->insert([
            ['level_number' => 1, 'nama_level' => 'Pemula', 'xp_min' => 0, 'xp_max' => 199],
            ['level_number' => 2, 'nama_level' => 'Pembelajar', 'xp_min' => 200, 'xp_max' => 349],
            ['level_number' => 3, 'nama_level' => 'Penjelajah', 'xp_min' => 350, 'xp_max' => 499],
            ['level_number' => 4, 'nama_level' => 'Ahli', 'xp_min' => 500, 'xp_max' => 699],
            ['level_number' => 5, 'nama_level' => 'Master', 'xp_min' => 700, 'xp_max' => 999999],
        ]);

        DB::table('badges')->insert([
            ['nama' => 'Computer Explorer', 'deskripsi' => 'Pelajari struktur komputer', 'icon_url' => null, 'kriteria' => 'Selesaikan Modul 1'],
            ['nama' => 'Memory Master', 'deskripsi' => 'Selesaikan semua materi memori', 'icon_url' => null, 'kriteria' => 'Selesaikan Modul 2'],
            ['nama' => 'System Solver', 'deskripsi' => 'Selesaikan 10 challenge', 'icon_url' => null, 'kriteria' => 'Total 10 challenge diselesaikan'],
            ['nama' => 'CPU Engineer', 'deskripsi' => 'Selesaikan materi CPU dan pipeline', 'icon_url' => null, 'kriteria' => 'Selesaikan Modul 4'],
        ]);

        DB::table('modules')->insert([
            ['kode_modul' => 'Modul 1', 'judul' => 'Computer System & Interconnection', 'sub_cpmk' => 'Sub-CPMK 1-2', 'urutan' => 1],
            ['kode_modul' => 'Modul 2', 'judul' => 'Memory & Storage', 'sub_cpmk' => 'Sub-CPMK 3', 'urutan' => 2],
            ['kode_modul' => 'Modul 3', 'judul' => 'I/O, Arithmetic & Instruction', 'sub_cpmk' => 'Sub-CPMK 4-5', 'urutan' => 3],
            ['kode_modul' => 'Modul 4', 'judul' => 'CPU, Control & Parallel Processing', 'sub_cpmk' => 'Sub-CPMK 6-7', 'urutan' => 4],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('daily_activity_log');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('user_module_progress');
        Schema::dropIfExists('user_ar_progress');
        Schema::dropIfExists('user_material_progress');
        Schema::dropIfExists('user_answers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('ar_content');
        Schema::dropIfExists('materi');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('levels');
    }
};
