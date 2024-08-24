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
            IN p_imagen_perfil TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_celular VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_genero VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_direccion VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_tipo_documento VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_departamento VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_municipio VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_fecha_nac DATE,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_contrasena VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN  
        )
        BEGIN
            DECLARE last_inserted_id INT;
            DECLARE v_iddepartamento INT;
            DECLARE v_idmunicipio INT;
            
           
            START TRANSACTION;

            
            IF EXISTS (SELECT 1 FROM users WHERE email = p_correo) THEN
                SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
                ROLLBACK; -- Deshacer la transacción si el correo ya existe
            ELSE
                -- Insertar en la tabla de usuarios
                INSERT INTO users (email, password, estado, id_rol) 
                VALUES (p_correo, p_contrasena, p_estado, 1); 

                -- Obtener el ID insertado en la tabla users
                SELECT LAST_INSERT_ID() INTO last_inserted_id;

                -- Obtener el ID del departamento
                SELECT id INTO v_iddepartamento FROM departamentos WHERE nombre = p_departamento LIMIT 1;

                -- Verificar si se encontró el departamento
                IF v_iddepartamento IS NULL THEN
                    SELECT 'Departamento no encontrado' AS mensaje;
                    ROLLBACK; -- Deshacer la transacción si no se encuentra el departamento
                ELSE
                    -- Obtener el ID del municipio
                    SELECT id INTO v_idmunicipio FROM municipios WHERE nombre = p_municipio LIMIT 1;

                    -- Verificar si se encontró el municipio
                    IF v_idmunicipio IS NULL THEN
                        SELECT 'Municipio no encontrado' AS mensaje;
                        ROLLBACK; -- Deshacer la transacción si no se encuentra el municipio
                    ELSE
                        -- Insertar en la tabla de superadmin
                        INSERT INTO superadmin (nombre, apellido, documento, imagen_perfil, celular, genero, direccion, id_tipo_documento, id_departamento, id_municipio, fecha_nac, id_autentication) 
                        VALUES (p_nombre, p_apellido, p_documento, p_imagen_perfil, p_celular, p_genero, p_direccion, p_tipo_documento, v_iddepartamento, v_idmunicipio, p_fecha_nac, last_inserted_id);

                        -- Confirmar la transacción
                        COMMIT;

                        SELECT 'Tu Asesor ha sido creado con éxito' AS mensaje;
                    END IF;
                END IF;
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
