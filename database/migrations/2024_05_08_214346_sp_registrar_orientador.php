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
            In p_celular varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_correo VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_contrasena VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_estado BOOLEAN  -- Sin coma aquí
        )
        BEGIN
        DECLARE last_inserted_id INT;
        
        IF EXISTS (SELECT 1 FROM users WHERE email = p_correo) THEN
        SELECT 'El correo electrónico ya ha sido registrado anteriormente' AS mensaje;
    ELSE
            INSERT INTO users (email, password, estado, id_rol) 
            VALUES (p_correo, p_contrasena, p_estado, 2);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
            INSERT INTO orientador (nombre, apellido, celular, id_autentication) 
            VALUES (p_nombre,p_apellido,p_celular, @last_inserted_id);
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
