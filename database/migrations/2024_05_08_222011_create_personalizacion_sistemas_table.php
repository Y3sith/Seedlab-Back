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
        Schema::create('personalizacion_sistema', function (Blueprint $table) {
            $table->id();
            $table->longBinary('imagen_logo');
            $table->string('nombre_sistema',50);
            $table->string('color_principal',10);
            $table->string('color_secundario',10);
            $table->string('color_terciario',10);
            $table->text('descripcion_footer');
            $table->string('paginaWeb', 20);
            $table->string('email', 50);
            $table->string('telefono', 15);
            $table->string('direccion', 25);
            $table->string('ubicacion', 50);
            $table->unsignedBigInteger('id_superadmin');
            $table->foreign('id_superadmin')->references('id')->on('superadmin');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalizacion_sistema');
    }
};
