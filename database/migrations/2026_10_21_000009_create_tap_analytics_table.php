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
        Schema::create('tap_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('nfc_tags')->cascadeOnDelete();
            $table->enum('mode_saat_diakses', ['kampus', 'kerja', 'umkm', 'hilang']);
            $table->timestamp('waktu_tap')->useCurrent()->index();
            $table->string('kota_lokasi_scan', 100)->nullable();
            $table->string('koordinat_gps_opsional', 100)->nullable();
            $table->string('jenis_perangkat_scanner', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tag_id', 'waktu_tap']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tap_analytics');
    }
};
