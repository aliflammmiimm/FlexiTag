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
        Schema::create('profile_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_profile_id')->constrained('personal_profiles')->cascadeOnDelete();
            $table->enum('jenis_platform', [
                'LinkedIn',
                'Instagram',
                'Portfolio',
                'GitHub',
                'TikTok',
                'Twitter',
                'Website',
                'Other',
            ]);
            $table->string('judul_tautan', 100);
            $table->string('sub_teks', 150)->nullable();
            $table->string('url_tujuan', 500);
            $table->unsignedSmallInteger('urutan_tampil')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['personal_profile_id', 'urutan_tampil']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_links');
    }
};
