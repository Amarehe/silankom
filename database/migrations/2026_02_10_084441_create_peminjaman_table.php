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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->foreignId('req_pinjam_id')->constrained('req_pinjam')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('barang_id')->constrained('barang', 'id_barang')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('tanggal_serah_terima');
            $table->enum('kondisi_barang', ['baik', 'rusak ringan', 'rusak berat']);
            $table->text('kelengkapan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->enum('status_peminjaman', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->enum('kondisi_kembali', ['baik', 'rusak ringan', 'rusak berat'])->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
