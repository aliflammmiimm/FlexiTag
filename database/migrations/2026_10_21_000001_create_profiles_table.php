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
        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('display_name', 100);
            $table->string('headline', 150)->nullable();
            $table->text('bio')->nullable(); // maks 500 karakter diatur pada validasi aplikasi
            $table->string('photo_path', 255)->nullable();
            $table->string('theme', 30)->default('sulawesi_heritage');
            $table->enum('active_mode', ['kampus', 'kerja', 'umkm', 'hilang'])->default('kampus')->index();
            $table->string('username', 20)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
