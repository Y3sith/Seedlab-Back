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
        Schema::create('empresas', function (Blueprint $table) {
            $table->string('documento', 50)->primary();
            $table->string('nombre',50);
            $table->string('apellido',50);
            $table->string('cargo',50);
            $table->string('razonSocial',50);
            $table->string('urlPagina');
            $table->string('telefono',10);
            $table->string('celular',13);
            $table->string('direccion',50);
            $table->string('profesion',50);
            $table->text('experiencia');
            $table->text('funciones');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
