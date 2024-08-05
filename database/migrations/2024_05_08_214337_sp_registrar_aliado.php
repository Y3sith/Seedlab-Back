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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_aliado;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_aliado(
            IN p_nombre VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            -- IN p_logo BLOB ,
            IN p_logo TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_descripcion VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_tipodato VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_ruta_multi TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_contrasena VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN
        )
        BEGIN
            DECLARE v_idtipodato INT;
            DECLARE v_id_aliado INT;
        
                    IF EXISTS (SELECT 1 FROM aliado WHERE nombre  = p_nombre limit 1) THEN
                        SELECT 'El nombre del aliado ya se encuentra registrado' AS mensaje, NULL AS id;
                    ELSE
                        IF EXISTS ( SELECT 1 FROM users WHERE email = p_correo limit 1) THEN
                            SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje, NULL AS id;
                        ELSE
                            SELECT id INTO v_idtipodato FROM tipo_dato WHERE tipo_dato.nombre = p_tipodato;
        
                            INSERT INTO users (email, password, estado, id_rol)
                            VALUES (p_correo, p_contrasena, p_estado, 3);
        
                            SELECT LAST_INSERT_ID() INTO @last_inserted_user_id;
        
                            INSERT INTO aliado (nombre, logo, descripcion, id_tipo_dato, ruta_multi, id_autentication)
                            VALUES (p_nombre, p_logo, p_descripcion, v_idtipodato, p_ruta_multi, @last_inserted_user_id);

                            SELECT LAST_INSERT_ID() INTO v_id_aliado;
                            
                            SELECT 'Se ha registrado exitosamente el aliado' AS mensaje,v_id_aliado AS id;
                        END IF;
                    END IF;
        END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_aliado;');
    }
};
