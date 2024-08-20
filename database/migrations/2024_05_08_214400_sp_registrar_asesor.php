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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_asesor;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_asesor(
        In p_nombre varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_apellido varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_documento varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_imagen_perfil text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_celular varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_genero varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_direccion varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_aliado varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, --  //no el id el nombre
        In p_tipo_documento varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_fecha_nacmento varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_municipio varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        In p_fecha_nac DATE,
        IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_contrasena VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        IN p_estado BOOLEAN  
)
BEGIN
    DECLARE v_idaliado VARCHAR(50); 
    DECLARE last_inserted_id INT;

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF EXISTS (SELECT 1 FROM users WHERE email = p_correo) THEN
        SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
    ELSE
    
        IF EXISTS (SELECT 1 FROM asesor WHERE celular = p_celular LIMIT 1) THEN
            SELECT 'El numero de celular ya ha sido registrado en el sistema' AS mensaje;
        ELSE

            INSERT INTO users (email, password, estado, id_rol) 
            VALUES (p_correo, p_contrasena, p_estado, 4);

            SELECT LAST_INSERT_ID() INTO @last_inserted_id;

            SELECT id INTO v_idaliado FROM aliado WHERE aliado.nombre = p_aliado;

            INSERT INTO asesor(nombre, apellido, documento, imagen_perfil, celular, genero,
           direccion, id_aliado, id_tipo_documento, id_municipio,fecha_nac,id_autentication) 
            VALUES (p_nombre, p_apellido, p_documento, p_imagen_perfil, p_celular, p_genero,
            p_direccion, v_idaliado, p_tipo_documento,p_municipio, p_fecha_nac,@last_inserted_id);

            SELECT 'Se ha registrado exitosamente el asesor' AS mensaje;
        END IF;
    END IF;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_asesor;');
    }
};
