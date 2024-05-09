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
        DB::unprepared('CREATE PROCEDURE sp_registrar_asesor(
            In p_nombre varchar(50),
            In p_aliado varchar(50),
            IN p_correo VARCHAR(50),
            IN p_contrasena VARCHAR(20),
            IN p_estado BOOLEAN  
        )
        BEGIN
             DECLARE v_idaliado VARCHAR(50); 
            START TRANSACTION;
            
            select id into v_idaliado from aliado where aliado.nombre = p_aliado;
        
            INSERT INTO autentications (correo, contrasena, estado, idrol) 
            VALUES (p_correo, p_contrasena, p_estado, 4);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
            INSERT INTO asesor(nombre,idaliado,idauth) 
            VALUES (p_nombre,v_idaliado,@last_inserted_id);
        
            COMMIT;
        END //');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
