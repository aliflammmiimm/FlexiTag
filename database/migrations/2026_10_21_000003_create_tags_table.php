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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_code', 12)->unique(); // Digunakan untuk rute publik /u/{id}
            $table->string('activation_code', 255)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['belum_aktif', 'aktif', 'nonaktif'])->default('belum_aktif')->index();
            $table->enum('disabled_by', ['owner', 'admin'])->nullable();
            $table->string('batch_id', 30)->nullable()->index();
            $table->dateTime('produced_at')->nullable();
            $table->dateTime('activated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
