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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_orientador;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_orientador(
            IN p_nombre varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_apellido varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_documento varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_imagen_perfil text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_direccion varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_celular varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_genero varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_fecha_nac VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_tipo_documento INT,
            In p_id_municipio INT,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_contrasena VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN  -- Sin coma aquí
        )
        BEGIN
        DECLARE last_inserted_id INT;

        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
        END;
        
        IF EXISTS (SELECT 1 FROM users WHERE email = p_correo) THEN
        SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
            
        ELSEIF EXISTS (SELECT 1 FROM orientador WHERE celular  = p_celular limit 1) THEN
			 SELECT 'El numero de celular ya ha sido registrado en el sistema' AS mensaje;
        ELSE
            INSERT INTO users (email, password, estado, id_rol) 
            VALUES (p_correo, p_contrasena, p_estado, 2);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
            INSERT INTO orientador (nombre, apellido, id_autentication, documento, imagen_perfil, direccion, celular, genero, fecha_nac, id_tipo_documento,id_municipio) 
            VALUES (p_nombre,p_apellido, @last_inserted_id,p_documento, p_imagen_perfil, p_direccion, p_celular, p_genero, p_fecha_nac, p_tipo_documento, p_id_municipio);

            SELECT 'El orientador ha sido creado con exito' AS mensaje;
    END IF;            
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_orientador;');
    }
};
