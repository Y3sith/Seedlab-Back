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
            $table->text('imagen_perfil')->nullable(); // 'text' no requiere collation, usa la predeterminada
            $table->string('celular', 13)->collation('utf8mb4_unicode_ci');
            $table->string('genero', 20)->collation('utf8mb4_unicode_ci');
            $table->date('fecha_nac'); // 'date' no requiere collation
            $table->string('direccion', 50)->collation('utf8mb4_unicode_ci');
            $table->timestamp('email_verified_at')->nullable(); // 'timestamp' no requiere collation
            $table->string('cod_ver', 10)->nullable()->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('id_autentication');
            $table->foreign('id_autentication')->references('id')->on('users');
            $table->unsignedBigInteger('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documento');
            $table->unsignedBigInteger('id_departamento');
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')->references('id')->on('municipios');
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
