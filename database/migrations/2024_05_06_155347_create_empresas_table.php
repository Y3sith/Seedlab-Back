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
        Schema::create('empresa', function (Blueprint $table) {
            $table->integer('documento', 50)->primary()->collation('utf8mb4_unicode_ci');
            $table->string('nombre',50);
            $table->string('cargo',50);
            $table->string('razonSocial',50);
            $table->string('url_pagina')->nullable();
            $table->string('telefono',10)->nullable();
            $table->string('celular',13);
            $table->string('direccion',50);
            $table->string('profesion',50);
            $table->string('correo',100);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->text('experiencia')->nullable();
            $table->text('funciones')->nullable();

            $table->integer('id_emprendedor');
            $table->foreign('id_emprendedor')->references('documento')->on('emprendedor');

            $table->unsignedBigInteger('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documento');
            $table->unsignedBigInteger('id_departamento');
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')->references('id')->on('municipios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
