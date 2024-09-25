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
        Schema::create('emprendedor', function (Blueprint $table) {
            $table->string('documento', 50)->primary()->collation('utf8mb4_unicode_ci');
            $table->string('nombre', 50)->collation('utf8mb4_unicode_ci');
            $table->string('apellido', 50)->collation('utf8mb4_unicode_ci');
            $table->text('imagen_perfil')->nullable();
            $table->string('celular', 13)->collation('utf8mb4_unicode_ci');
            $table->string('genero', 20)->collation('utf8mb4_unicode_ci');
            $table->date('fecha_nac')->collation('utf8mb4_unicode_ci');
            $table->string('direccion', 50)->collation('utf8mb4_unicode_ci');
            $table->timestamp('email_verified_at')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('cod_ver', 10)->nullable()->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('id_autentication')->collation('utf8mb4_unicode_ci');
            $table->foreign('id_autentication')->references('id')->on('users')->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('id_tipo_documento')->collation('utf8mb4_unicode_ci');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documento')->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('id_departamento')->collation('utf8mb4_unicode_ci');
            $table->foreign('id_departamento')->references('id')->on('departamentos')->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('id_municipio')->collation('utf8mb4_unicode_ci');
            $table->foreign('id_municipio')->references('id')->on('municipios')->collation('utf8mb4_unicode_ci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emprendedor');
    }
};
