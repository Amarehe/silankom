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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreignId('admin_penerima_id')->nullable()->after('admin_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nomor_surat_pengembalian')->nullable()->unique()->after('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['admin_penerima_id']);
            $table->dropColumn('admin_penerima_id');
            $table->dropColumn('nomor_surat_pengembalian');
        });
    }
};
