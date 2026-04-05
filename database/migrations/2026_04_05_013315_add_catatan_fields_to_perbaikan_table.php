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
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->text('catatan_pengambilan')->nullable()->after('keterangan');
            $table->text('catatan_pengantaran')->nullable()->after('catatan_pengambilan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->dropColumn(['catatan_pengambilan', 'catatan_pengantaran']);
        });
    }
};
