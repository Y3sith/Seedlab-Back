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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_validar_correo;');
        DB::unprepared("CREATE PROCEDURE sp_validar_correo(
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_cod_ver VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        )
        BEGIN
            DECLARE v_id_autenticacion INT;
        
            SELECT id INTO v_id_autenticacion FROM users WHERE email = p_correo;
        
            IF v_id_autenticacion IS NULL THEN
                SELECT 'El correo electrónico no esta registrado' AS mensaje;
            ELSE
                IF EXISTS (
                    SELECT 1 
                    FROM emprendedor 
                    WHERE id_autentication = v_id_autenticacion 
                    AND email_verified_at IS NOT NULL
                ) THEN
                    SELECT 'El correo electrónico ya ha sido verificado anteriormente' AS mensaje;
                ELSE
                    UPDATE emprendedor
                    SET email_verified_at = CURRENT_TIMESTAMP()
                    WHERE id_autentication = v_id_autenticacion
                        AND email_verified_at IS NULL
                        AND cod_ver = p_cod_ver;
        
                    IF ROW_COUNT() > 0 THEN
                        SELECT 'Tu correo ha sido verificado exitosamente' AS mensaje;
                    ELSE
                        SELECT 'El código de verificación proporcionado no coincide' AS mensaje;
                    END IF;
                END IF;
            END IF;
        END;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_validar_correo;');
    }
};
