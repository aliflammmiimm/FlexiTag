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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['lost_item', 'contact', 'abuse_report'])->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // Pemilik tag penerima jika lost_item
            $table->string('sender_name', 100)->nullable();
            $table->string('location', 150)->nullable();
            $table->text('message');
            $table->string('contact', 100)->nullable(); // No WA/HP atau email pengirim
            $table->dateTime('read_at')->nullable();
            $table->dateTime('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
