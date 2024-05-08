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
        Schema::create('aliados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('descripcion');
            $table->string('logo');
            $table->string('rutaMulti');
            $table->unsignedBigInteger('id_autentication');
            $table->foreign('id_autentication')->references('id')->on('autentications');
            $table->unsignedBigInteger('id_tipo_dato');
            $table->foreign('id_tipo_dato')->references('id')->on('tipo_datos');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliados');
    }
};
