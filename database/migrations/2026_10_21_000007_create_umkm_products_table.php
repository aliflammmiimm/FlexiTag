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
        Schema::create('umkm_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_profile_id')->constrained('umkm_profiles')->cascadeOnDelete();
            $table->string('nama_produk', 150);
            $table->string('foto_produk_url', 255)->nullable();
            $table->decimal('harga', 12, 2)->default(0.00);
            $table->string('satuan', 30)->default('/pcs'); // contoh: /porsi, /pak, /150g
            $table->text('deskripsi_produk')->nullable();
            $table->enum('label_promosi', ['tanpa_label', 'unggulan', 'paling_laris'])->default('tanpa_label');
            $table->boolean('status_aktif')->default(true);
            $table->unsignedSmallInteger('urutan_tampil')->default(1);
            $table->timestamps();

            $table->index(['umkm_profile_id', 'status_aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_products');
    }
};
