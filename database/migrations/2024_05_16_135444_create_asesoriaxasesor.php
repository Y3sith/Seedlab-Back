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
        Schema::create('asesoriaxasesor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_asesoria');
            $table->unsignedBigInteger('id_asesor');
            $table->foreign('id_asesoria')->references('id')->on('asesoria')->onDelete('cascade');
            $table->foreign('id_asesor')->references('id')->on('aliado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesoriaxasesor');
    }
};
