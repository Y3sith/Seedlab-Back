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
            $table->json('respuestas_json');
            $table->boolean('verform_pr')->nullable();
            $table->boolean('verform_se')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('id_empresa');
            $table->foreign('id_empresa')->references('documento')->on('empresa');
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
