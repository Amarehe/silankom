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
        Schema::table('req_pinjam', function (Blueprint $table) {
            $table->string('nama_pemohon')->nullable()->after('user_id');
            $table->string('nip_pemohon')->nullable()->after('nama_pemohon');
            $table->string('jabatan_pemohon')->nullable()->after('nip_pemohon');
            $table->string('unitkerja_pemohon')->nullable()->after('jabatan_pemohon');
            $table->foreignId('submitted_by')->nullable()->after('unitkerja_pemohon')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('perbaikan', function (Blueprint $table) {
            $table->string('nama_pemohon')->nullable()->after('pemohon_id');
            $table->string('nip_pemohon')->nullable()->after('nama_pemohon');
            $table->string('jabatan_pemohon')->nullable()->after('nip_pemohon');
            $table->string('unitkerja_pemohon')->nullable()->after('jabatan_pemohon');
            $table->foreignId('submitted_by')->nullable()->after('unitkerja_pemohon')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('req_dukungan', function (Blueprint $table) {
            $table->string('nama_pemohon')->nullable()->after('pemohon_id');
            $table->string('nip_pemohon')->nullable()->after('nama_pemohon');
            $table->string('jabatan_pemohon')->nullable()->after('nip_pemohon');
            $table->string('unitkerja_pemohon')->nullable()->after('jabatan_pemohon');
            $table->foreignId('submitted_by')->nullable()->after('unitkerja_pemohon')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('req_pinjam', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['nama_pemohon', 'nip_pemohon', 'jabatan_pemohon', 'unitkerja_pemohon', 'submitted_by']);
        });

        Schema::table('perbaikan', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['nama_pemohon', 'nip_pemohon', 'jabatan_pemohon', 'unitkerja_pemohon', 'submitted_by']);
        });

        Schema::table('req_dukungan', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['nama_pemohon', 'nip_pemohon', 'jabatan_pemohon', 'unitkerja_pemohon', 'submitted_by']);
        });
    }
};
