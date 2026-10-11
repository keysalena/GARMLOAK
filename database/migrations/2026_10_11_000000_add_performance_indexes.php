<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index untuk kolom yang sering difilter/diurutkan oleh API.
     * Aman dijalankan ulang: index yang sudah ada dilewati.
     *
     * SEBELUM migrate, pastikan tidak ada duplikat di user_progress:
     *   SELECT user_id, materi_id, COUNT(*) c FROM user_progress
     *   GROUP BY user_id, materi_id HAVING c > 1;
     * Jika ada hasilnya, hapus duplikatnya dulu (kalau tidak, unique index gagal dibuat).
     */
    public function up(): void
    {
        $this->addIndex('materi', ['module_id', 'tipe']);
        $this->addIndex('ar_content', ['module_id', 'urutan']);
        $this->addIndex('user_module_progress', ['user_id', 'module_id']);
        $this->addIndex('users', ['role', 'total_xp']);   // leaderboard global
        $this->addIndex('users', ['kelas_id', 'role', 'total_xp']); // leaderboard kelas

        // Unique: mencegah XP ganda dari klik ganda / request paralel
        $this->addIndex('user_progress', ['user_id', 'materi_id'], unique: true);
    }

    public function down(): void
    {
        // Sengaja kosong: index ini aman dibiarkan.
    }

    private function addIndex(string $table, array $columns, bool $unique = false): void
    {
        if (!Schema::hasTable($table) || Schema::hasIndex($table, $columns)) {
            return;
        }

        Schema::table($table, function (Blueprint $t) use ($columns, $unique) {
            $unique ? $t->unique($columns) : $t->index($columns);
        });
    }
};