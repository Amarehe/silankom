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
        Schema::table('req_dukungan', function (Blueprint $table) {
            $table->string('nama_kegiatan')->after('nomor_nodis')->nullable();
            $table->text('deskripsi_kegiatan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('req_dukungan', function (Blueprint $table) {
            $table->dropColumn('nama_kegiatan');
            $table->string('deskripsi_kegiatan')->change();
        });
    }
};
