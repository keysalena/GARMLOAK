<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            // Menggunakan char(36) atau uuid karena user_id Anda berbentuk UUID
            $table->uuid('user_id'); 
            $table->unsignedBigInteger('materi_id');
            $table->timestamps();

            // (Opsional) Menambahkan foreign key jika diperlukan
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('materi_id')->references('materi_id')->on('materi')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_progress');
    }
};