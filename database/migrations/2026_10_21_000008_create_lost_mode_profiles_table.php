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
        Schema::create('lost_mode_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->unique()->constrained('nfc_tags')->cascadeOnDelete();
            $table->text('pesan_darurat_penemu');
            $table->string('no_hp_penerima_relay', 255); // Tersimpan terenkripsi/terlindungi
            $table->string('info_imbalan_opsional', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_mode_profiles');
    }
};
