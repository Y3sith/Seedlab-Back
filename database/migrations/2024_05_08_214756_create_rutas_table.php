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
        Schema::create('ruta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',100);
            $table->date('fecha_creacion');
            $table->boolean('estado');
            $table->longBinary('imagen_ruta');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta');
    }
};
