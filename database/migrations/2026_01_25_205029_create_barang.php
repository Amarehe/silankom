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
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('nama_barang');

            $table->foreignId('kategori_id')->constrained('kategori_barang', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('merek_id')->constrained('merek', 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('serial_number')->unique()->nullable();
            $table->string('label')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'perlu_perbaikan'])->default('baik');
            $table->year('tahun')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam'])->default('tersedia');
            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate(); // User yang menginput

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
