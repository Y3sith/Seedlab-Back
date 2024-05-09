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
        DB::unprepared('CREATE PROCEDURE sp_registrar_orientador(
            IN p_nombre varchar(50),
            In p_apellido varchar(50),
            In p_celular varchar(13),
            IN p_correo VARCHAR(50),
            IN p_contrasena VARCHAR(20),
            IN p_estado BOOLEAN  -- Sin coma aquí
        )
        BEGIN
            START TRANSACTION;
        
            INSERT INTO autentications (correo, contrasena, estado, idrol) 
            VALUES (p_correo, p_contrasena, p_estado, 2);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
            INSERT INTO orientador (nombre, apellido,celular,idauth) 
            VALUES (p_nombre,p_apellido,p_celular, @last_inserted_id);
        
            -- Confirmar la transacción
            COMMIT;
        END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
