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
        Schema::create('review_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->morphs('reviewable');
            $table->tinyInteger('rating')->comment('Rating keseluruhan 1-5');
            $table->tinyInteger('aspek_kecepatan')->comment('Rating kecepatan layanan 1-5');
            $table->tinyInteger('aspek_kualitas')->comment('Rating kualitas hasil 1-5');
            $table->tinyInteger('aspek_pelayanan')->comment('Rating keramahan petugas 1-5');
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->unique(['reviewable_type', 'reviewable_id'], 'review_layanan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_layanan');
    }
};
