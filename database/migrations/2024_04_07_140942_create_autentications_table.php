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
        Schema::create('autentications', function (Blueprint $table) {
            $table->id();
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->boolean('estado');
            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id')->on('rol');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autentications');
    }
};
