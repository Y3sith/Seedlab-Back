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
        Schema::create('personalizacion_sistemas', function (Blueprint $table) {
            $table->id();
            $table->string('imagen_logo');
            $table->string('nombre_sistema',50);
            $table->string('color_principal',10);
            $table->string('color_secundario',10);
            $table->unsignedBigInteger('id_superadmin');
            $table->foreign('id_superadmin')->references('id')->on('super_admins');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalizacion_sistemas');
    }
};
