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
        Schema::create('superadmin', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('documento', 50);
            $table->text('imagen_perfil');
            $table->string('celular', 13);
            $table->string('genero', 20);
            $table->date('fecha_nac');
            $table->string('direccion', 50);
            $table->unsignedBigInteger('id_autentication');
            $table->foreign('id_autentication')->references('id')->on('users');
            $table->unsignedBigInteger('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documento');
            $table->unsignedBigInteger('id_departamento');
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')->references('id')->on('municipios');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
