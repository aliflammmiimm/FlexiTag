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
        Schema::create('mode_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('mode', ['kampus', 'kerja', 'umkm', 'hilang'])->index();
            $table->json('data'); // Menyimpan data dinamis per mode (array katalog UMKM, info kampus, CV kerja, info darurat)
            $table->timestamps();

            $table->unique(['user_id', 'mode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_data');
    }
};
