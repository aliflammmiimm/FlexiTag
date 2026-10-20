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
        Schema::create('emergency_relay_messages', function (Blueprint $table) {
            $table->id();
            $table->string('id_pesan', 30)->unique();
            $table->foreignId('tag_id')->constrained('nfc_tags')->cascadeOnDelete();
            $table->text('isi_pesan_penemu');
            $table->string('kontak_penemu_opsional', 100)->nullable();
            $table->string('lokasi_gps_penemuan', 255)->nullable();
            $table->enum('status_relay_wa', ['pending', 'terkirim', 'gagal', 'selesai'])->default('terkirim')->index();
            $table->timestamp('waktu_kirim')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_relay_messages');
    }
};
