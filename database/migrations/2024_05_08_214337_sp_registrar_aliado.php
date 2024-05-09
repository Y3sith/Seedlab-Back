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
        DB::unprepared('CREATE PROCEDURE sp_registrar_aliado(
            In p_nombre varchar(50),
            /*In p_logo varchar(50),*/
            In p_descripcion varchar(255),
            In p_tipodato varchar(20),
            In p_ruta varchar(50),
            IN p_correo VARCHAR(50),
            IN p_contrasena VARCHAR(20),
            IN p_estado BOOLEAN  
        )
        BEGIN
             DECLARE v_idtipodato VARCHAR(50); 
            START TRANSACTION;
            
            select id into v_idtipodato from tipodato where tipodato.nombre = p_tipodato;
        
            INSERT INTO autentications (correo, contrasena, estado, idrol) 
            VALUES (p_correo, p_contrasena, p_estado, 3);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
        
            INSERT INTO aliado (nombre,logo,descripcion,idtipodato,ruta,idauth) 
            VALUES (p_nombre,null,p_descripcion,v_idtipodato,p_ruta,@last_inserted_id);
        
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
