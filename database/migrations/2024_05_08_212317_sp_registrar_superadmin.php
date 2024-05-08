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
        DB::unprepared('CREATE PROCEDURE sp_registrar_superadmin(
        IN p_nombre VARCHAR(50),
        IN p_apellido VARCHAR(50),
        IN p_correo VARCHAR(50),
        IN p_contrasena VARCHAR(20),
        IN p_estado BOOLEAN  
    )
    BEGIN
        DECLARE last_inserted_id INT;
    
        START TRANSACTION;
        
        INSERT INTO auth (correo, contrasena, estado, idrol) 
        VALUES (p_correo, p_contrasena, p_estado, 1);
        
        SELECT LAST_INSERT_ID() INTO last_inserted_id;
        
        INSERT INTO superadmin (nombre, apellido, idauth) 
        VALUES (p_nombre, p_apellido, last_inserted_id);
        
        COMMIT;
    END;');
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
