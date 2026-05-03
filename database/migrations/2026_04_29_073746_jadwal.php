<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->time('waktu_aktif_pertama');
            $table->time('waktu_aktif_kedua')->nullable();
            $table->integer('lama_operasi'); // dalam menit
            $table->boolean('aktif')->default(true);
            $table->json('hari')->nullable(); // ['senin', 'selasa', ...] untuk fleksibilitas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
