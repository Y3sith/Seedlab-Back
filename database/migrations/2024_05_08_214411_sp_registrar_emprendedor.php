<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_emprendedor;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_emprendedor(
            IN p_num_documento VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_nombretipodoc VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_nombre VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_apellido VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            In p_imagen_perfil text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_celular VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_genero VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_fecha_nac VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_municipio VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_direccion VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_password VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN,
            IN p_cod_ver VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        )
        BEGIN
            DECLARE v_idtipodoc INT;
            DECLARE v_idmunicipio INT;
            DECLARE v_fecha_nac DATE;
        
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
			IF EXISTS (SELECT 1 FROM emprendedor WHERE documento  = p_num_documento limit 1) THEN
				SELECT 'El numero de documento ya ha sido registrado en el sistema' AS mensaje;
			ELSE
				IF EXISTS ( SELECT 1 FROM emprendedor WHERE celular = p_celular limit 1) THEN
					SELECT 'Este numero de celular ya ha sido registrado anteriormente' AS mensaje;
				ELSE
                    IF EXISTS ( SELECT 1 FROM users WHERE email = p_correo limit 1) THEN
					    SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
				    ELSE
						SELECT id INTO v_idtipodoc FROM tipo_documento WHERE nombre = p_nombretipodoc LIMIT 1;
						SELECT id INTO v_idmunicipio FROM municipios WHERE nombre = p_municipio LIMIT 1;
						SET v_fecha_nac = STR_TO_DATE(p_fecha_nac, '%Y-%m-%d');
						
						INSERT INTO users (email, password, estado, id_rol) 
						VALUES (p_correo, p_password, p_estado, 5);
						
						SELECT LAST_INSERT_ID() INTO @last_inserted_id;
						
						INSERT INTO emprendedor (documento, id_tipo_documento, nombre, apellido, imagen_perfil, celular, genero, fecha_nac, id_municipio, direccion, id_autentication, cod_ver) 
						VALUES (p_num_documento, v_idtipodoc, p_nombre, p_apellido, p_imagen_perfil, p_celular, p_genero, v_fecha_nac, v_idmunicipio, p_direccion, @last_inserted_id, p_cod_ver);

                        SELECT 'Tu usuario ha sido creado con exito' AS mensaje;
				END IF;
                end if;
			END IF;
	END");
    }

    //

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_emprendedor;');

    }
};
