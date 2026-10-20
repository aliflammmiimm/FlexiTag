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
        Schema::create('nfc_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('tag_batches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kode_aktivasi', 25)->unique();
            $table->string('hardware_uid', 50)->unique();
            $table->string('nama_custom_merch', 100)->nullable();
            $table->enum('status_tag', ['tersedia', 'aktif', 'diblokir'])->default('tersedia')->index();
            $table->boolean('is_kill_switch_on')->default(false);
            $table->enum('active_mode', ['kampus', 'kerja', 'umkm', 'hilang'])->default('kampus')->index();
            $table->timestamp('tanggal_aktivasi')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status_tag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_tags');
    }
};
