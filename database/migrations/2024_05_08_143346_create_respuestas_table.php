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
        Schema::create('respuesta', function (Blueprint $table) {
            $table->id();
            $table->string('opcion', 10)->nullable();
            $table->text('texto_res')->nullable();
            $table->double('valor');
            $table->boolean('verform_pr')->nullable();
            $table->boolean('verform_se')->nullable();
            $table->timestamp('fecha_reg');
            $table->unsignedBigInteger('id_pregunta');
            $table->foreign('id_pregunta')->references('id')->on('pregunta');
            $table->string('id_empresa');
            $table->foreign('id_empresa')->references('documento')->on('empresa');
            $table->unsignedBigInteger('id_subpregunta')->nullable();
            $table->foreign('id_subpregunta')->references('id')->on('subpregunta');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuesta');
    }
};
