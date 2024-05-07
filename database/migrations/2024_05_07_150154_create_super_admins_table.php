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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id')->on('rols');
            $table->unsignedBigInteger('id_autentication');
            $table->foreign('id_autentication')->references('id')->on('autentications');
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
