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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('id_pesanan', 30)->unique();
            $table->string('nama_pembeli', 100);
            $table->string('kota_pembeli', 100);
            $table->string('no_wa_pembeli', 25);
            $table->enum('jenis_produk_dipesan', [
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
            $table->foreignId('tag_id')->nullable()->constrained('nfc_tags')->nullOnDelete();
            $table->decimal('total_harga', 12, 2)->default(0.00);
            $table->enum('status_pengiriman', ['pending', 'diproses', 'dikirim', 'selesai'])->default('pending')->index();
            $table->timestamp('tanggal_pesanan')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
