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
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->string('urlImagenSmall')->nullable();
            $table->string('urlImagenMedium')->nullable();
            $table->string('urlImagenLarge')->nullable();
            //$table->text('urlImagen');
            $table->boolean('estadobanner');
            $table->unsignedBigInteger('id_aliado');
            $table->foreign('id_aliado')->references('id')->on('aliado')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner');
    }
};
