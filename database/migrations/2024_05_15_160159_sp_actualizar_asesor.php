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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_asesor');
        DB::unprepared("CREATE PROCEDURE sp_actualizar_asesor(
            IN p_id INT,
            IN p_nombre VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_apellido VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_celular VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_aliado VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_contrasena VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN  
        )
        BEGIN
    DECLARE v_idaliado INT;
    DECLARE v_idauth INT;
    DECLARE v_correo_actual VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;

    SELECT id INTO v_idaliado FROM aliado WHERE nombre = p_aliado;

    SELECT id_autentication INTO v_idauth FROM asesor WHERE id = p_id;

    SELECT email INTO v_correo_actual FROM users WHERE id = v_idauth;

    IF v_correo_actual != p_correo THEN

        IF EXISTS (SELECT 1 FROM users WHERE email = p_correo AND id != v_idauth) THEN
            SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
        END IF;
    END IF;

    UPDATE users
    SET email = p_correo,
        password = p_contrasena,
        estado = p_estado
    WHERE id = v_idauth;

    UPDATE asesor
    SET nombre = p_nombre,
        apellido = p_apellido,
        celular = p_celular,
        id_aliado = v_idaliado
    WHERE id = p_id;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_asesor;');
    }
};
