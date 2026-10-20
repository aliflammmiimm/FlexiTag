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
        Schema::create('personal_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('mode_type', ['kampus', 'kerja']);
            $table->string('foto_profil_url', 255)->nullable();
            $table->string('nama_tampilan', 100);
            $table->text('bio_singkat')->nullable();
            $table->string('email_publik', 150)->nullable();
            $table->string('no_telepon_publik', 25)->nullable();
            $table->string('lokasi_kota', 100)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'mode_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_profiles');
    }
};
