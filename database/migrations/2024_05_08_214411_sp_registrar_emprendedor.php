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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_superadmin;');
        DB::unprepared("CREATE PROCEDURE sp_registrar_emprendedor(
            IN p_num_documento varchar(20),
            In p_nombretipodoc varchar(20),
            In p_nombre varchar(50),
            In p_apellido varchar(50),
            In p_celular varchar(13),
            In p_genero varchar(20),
            In p_fecha_nac varchar(50),
            In p_municipio varchar(50),
            In p_direccion varchar(50),
            IN p_correo VARCHAR(50),
            IN p_contrasena VARCHAR(20),
            IN p_estado BOOLEAN
        )
        BEGIN
             DECLARE v_idtipodoc VARCHAR(50); 
             DECLARE v_idmunicipio VARCHAR(50); 
             DECLARE v_fecha_nac DATE;
            START TRANSACTION;
            
            select id into v_idtipodoc from tipo_documento where tipo_documento.nombre = p_nombretipodoc;
            select id into v_idmunicipio from municipios where municipios.nombre_mun = p_municipio;
            SET v_fecha_nac = STR_TO_DATE(p_fecha_nac, '%Y-%m-%d');
        
            INSERT INTO auth (correo, contrasena, estado, idrol) 
            VALUES (p_correo, p_contrasena, p_estado, 5);
            
            SELECT LAST_INSERT_ID() INTO @last_inserted_id;
        
            INSERT INTO emprendedor (num_documento,idtipodoc,nombre,apellido,celular,genero,fecha_nac,id_municipio,direccion,idauth) 
            VALUES (p_num_documento,v_idtipodoc,p_nombre,p_apellido,p_celular,p_genero,v_fecha_nac,v_idmunicipio,p_direccion, @last_inserted_id);
        
            COMMIT;
        END");
    }

    //

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
