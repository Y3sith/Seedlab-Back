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
        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->string('opcion', 10);
            $table->text('texto_res');
            $table->double('valor');
            $table->unsignedBigInteger('id_pregunta');
            $table->foreign('id_pregunta')->references('id')->on('preguntas');
            $table->unsignedBigInteger('id_emprendedor');
            $table->foreign('id_emprendedor')->references('id')->on('emprendedors');
            $table->unsignedBigInteger('id_subpregunta');
            $table->foreign('id_subpregunta')->references('id')->on('subpreguntas');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas');
    }
};
