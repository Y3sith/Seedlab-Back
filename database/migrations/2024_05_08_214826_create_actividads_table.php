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
        Schema::create('actividad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',50);
            $table->text('descripcion');
            $table->text('fuente');
            $table->unsignedBigInteger('id_tipo_dato');
            $table->foreign('id_tipo_dato')->references('id')->on('tipo_dato');
            $table->unsignedBigInteger('id_asesor');
            $table->foreign('id_asesor')->references('id')->on('asesor');
            $table->unsignedBigInteger('id_ruta');
            $table->foreign('id_ruta')->references('id')->on('ruta');
            $table->unsignedBigInteger('id_aliado');
            $table->foreign('id_aliado')->references('id')->on('aliado');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividads');
    }
};
