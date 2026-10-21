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
            $table->string('order_no', 20)->unique();
            $table->string('customer_name', 100);
            $table->string('whatsapp', 30);
            $table->string('email', 150);
            $table->enum('delivery_method', ['ambil', 'kirim'])->default('kirim');
            $table->text('address')->nullable();
            $table->string('shape', 50); // bulat, persegi, custom
            $table->string('size', 50);  // standar_4x4, 5x5, custom
            $table->string('color', 50); // motif toraja, saqbe bugis, akrilik bening, kayu ukir
            $table->text('notes')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            $table->enum('status', [
                'menunggu_pembayaran',
                'pembayaran_dikonfirmasi',
                'desain_disetujui',
                'dicetak',
                'dirakit_dan_diuji',
                'siap_diambil_dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran')->index();
            $table->string('design_file_path', 255)->nullable();
            $table->string('payment_proof_path', 255)->nullable();
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
