<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_superadmin;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_superadmin(
        IN p_nombre VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_apellido VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_documento VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_imagen_perfil text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_celular varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_genero varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_direccion varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_tipo_documento varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_municipio varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_fecha_nac DATE,
        IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_contrasena VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_estado BOOLEAN  
    )
    BEGIN
    DECLARE last_inserted_id INT;

    IF EXISTS (SELECT 1 FROM users WHERE email = p_correo) THEN
        SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
    ELSE
        INSERT INTO users (email, password, estado, id_rol) 
        VALUES (p_correo, p_contrasena, p_estado, 1); 

        SELECT LAST_INSERT_ID() INTO last_inserted_id;

        INSERT INTO superadmin (nombre, apellido, documento, imagen_perfil, celular, genero, direccion ,id_tipo_documento, id_municipio, fecha_nac, id_autentication ) 
        VALUES (p_nombre, p_apellido, p_documento, p_imagen_perfil, p_celular, p_genero, p_direccion, p_tipo_documento, p_municipio, p_fecha_nac, last_inserted_id );


        SELECT 'Tu SuperAdmin ha sido creado con exito' AS mensaje;
    END IF;
END");
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_superadmin;');
    }
};
