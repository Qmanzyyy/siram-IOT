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
        Schema::create('device_controls', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique()->comment('Nama unik alat (contoh: pompa_01)');
            $table->enum('mode', ['auto', 'manual'])->default('auto')->comment('Mode operasi alat');
            $table->boolean('manual_on')->default(false)->comment('Status pompa saat mode manual');
            $table->timestamp('last_heartbeat')->nullable()->comment('Waktu terakhir ESP32 konek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_controls');
    }
};
