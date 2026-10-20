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
        Schema::create('umkm_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('logo_usaha_url', 255)->nullable();
            $table->string('nama_usaha', 120);
            $table->text('deskripsi_usaha')->nullable();
            $table->text('alamat_toko')->nullable();
            $table->string('koordinat_gps_toko', 100)->nullable();
            $table->string('no_wa_pemesanan', 25);
            $table->string('link_instagram', 255)->nullable();
            $table->string('link_brosur_pdf', 255)->nullable();
            $table->string('link_ulasan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_profiles');
    }
};
