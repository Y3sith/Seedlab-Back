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
        Schema::create('tipo_dato', function (Blueprint $table) {
            $table->id();
            $table->string('video');
            $table->string('mltimedia');
            $table->string('imagen');
            $table->string('pdf');
            $table->text('texto');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_dato');
    }
};
