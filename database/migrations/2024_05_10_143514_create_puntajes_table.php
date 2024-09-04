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
        Schema::create('puntaje', function (Blueprint $table) {
            $table->id();
            $table->double('info_general');
            $table->double('info_financiera');
            $table->double('info_mercado');
            $table->double('info_trl');
            $table->double('info_tecnica');
            $table->string('documento_empresa');
            $table->unsignedTinyInteger('ver_form');
            $table->foreign('documento_empresa')->references('documento')->on('empresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntaje');
    }
};
