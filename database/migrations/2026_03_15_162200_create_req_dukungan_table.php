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
        Schema::create('req_dukungan', function (Blueprint $table) {
            $table->id();

            // Diisi oleh Pemohon
            $table->string('nomor_nodis');
            $table->string('deskripsi_kegiatan');
            $table->string('ruangan');
            $table->date('tgl_kegiatan');
            $table->string('waktu');
            $table->json('req_barang');
            $table->enum('status_dukungan', ['belum_didukung', 'didukung', 'tidak_didukung'])->default('belum_didukung');
            $table->string('keterangan')->nullable();
            $table->foreignId('pemohon_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            // Diisi oleh Admin (saat approval)
            $table->string('alasan_ditolak')->nullable();
            $table->date('tgl_disetujui')->nullable();
            $table->json('barang_diberikan')->nullable();
            $table->string('catatan_admin')->nullable();
            $table->foreignId('pic_dukungan_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('req_dukungan');
    }
};
