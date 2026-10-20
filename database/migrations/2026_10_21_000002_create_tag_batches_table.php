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
        Schema::create('tag_batches', function (Blueprint $table) {
            $table->id();
            $table->string('id_batch', 50)->unique();
            $table->enum('jenis_produk', [
                'gantungan_akrilik',
                'gelang_karet',
                'lanyard_tenun',
                'stiker_nfc',
            ]);
            $table->enum('edisi_budaya', [
                'pateddong',
                'saqbe',
                'sulapa_eppa',
                'pabarre_allo',
            ]);
            $table->enum('spesifikasi_ic', ['NTAG213', 'NTAG215']);
            $table->unsignedInteger('jumlah_tag');
            $table->string('prefix_kode', 10)->default('FLX');
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_batches');
    }
};
