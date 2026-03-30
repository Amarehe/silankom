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
        Schema::create('perbaikan', function (Blueprint $table) {
            $table->id();

            // Diisi oleh User (Pemohon)
            $table->foreignId('pemohon_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('kategori_id')->constrained('kategori_barang')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('merek_id')->constrained('merek')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nm_barang');
            $table->date('tgl_pengajuan');
            $table->text('keluhan');
            $table->integer('jumlah')->default(1);
            $table->string('nodis');

            // Diisi oleh Admin/Teknisi saat proses
            $table->string('serial_number')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'diajukan',
                'diproses',
                'selesai',
                'tidak_bisa_diperbaiki',
            ])->default('diajukan');
            $table->string('no_surat_perbaikan')->nullable();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikan');
    }
};
