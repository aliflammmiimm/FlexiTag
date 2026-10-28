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
        Schema::table('messages', function (Blueprint $table) {
            // Ubah tipe kolom type menjadi string agar dinamis mendukung berbagai jenis notifikasi admin
            $table->string('type', 50)->default('contact')->change();

            if (! Schema::hasColumn('messages', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->nullOnDelete();
            }

            if (! Schema::hasColumn('messages', 'action_url')) {
                $table->string('action_url', 255)->nullable()->after('contact');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'action_url')) {
                $table->dropColumn('action_url');
            }

            if (Schema::hasColumn('messages', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
        });
    }
};
