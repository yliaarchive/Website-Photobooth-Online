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
        Schema::create('photo_frames', function (Blueprint $table) {
            $table->id();
            $table->string('nama_frame');
            $table->string('tema')->nullable();
            $table->foreignId('category_id')->constrained('frame_categories');
            $table->string('gambar_frame');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_frames');
    }
};
