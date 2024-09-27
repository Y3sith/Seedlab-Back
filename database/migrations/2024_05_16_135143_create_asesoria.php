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
        Schema::create('asesoria', function (Blueprint $table) {
            $table->id();
            $table->string('Nombre_sol', 100);
            $table->string('notas', 255)->nullable();
            $table->boolean('isorientador')->nullable();
            $table->boolean('asignacion')->default(false);;
            $table->dateTime('fecha');
            $table->unsignedBigInteger('id_aliado')->nullable();
            $table->foreign('id_aliado')->references('id')->on('aliado');
            $table->unsignedBigInteger('id_orientador')->nullable();
            $table->foreign('id_orientador')->references('id')->on('orientador');
            $table->string('doc_emprendedor', 50)->collation('utf8mb4_unicode_ci');
            $table->foreign('doc_emprendedor')->references('documento')->on('emprendedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesoria');
    }
};
