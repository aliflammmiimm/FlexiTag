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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('relay_wa_otomatis_aktif')->default(true);
            $table->boolean('notifikasi_email_pesanan')->default(true);
            $table->boolean('mode_maintenance')->default(false);
            $table->string('no_wa_cs_pusat', 25)->default('6281234567890');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
