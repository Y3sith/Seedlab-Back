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
            $table->double('info_gen');
            $table->double('info_fin');
            $table->double('info_mer');
            $table->double('info_op');
            $table->double('info_trl');
            $table->string('documento_emp');
            $table->unsignedTinyInteger('ver_form');
            $table->foreign('documento_emp')->references('documento')->on('empresa');
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
