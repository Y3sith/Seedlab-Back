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
        DB::unprepared('DROP PROCEDURE IF EXISTS crearEmpresaYApoyo');
        DB::unprepared('CREATE PROCEDURE crearEmpresaYApoyo(
    IN p_nombreEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_documentoEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_cargoEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_razonSocial VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_urlPagina VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_telefonoEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_celularEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_direccionEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_correoEmpresa VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_profesion VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_experiencia VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_funciones VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_idTipoDocumentoEmpresa INT,
    IN p_idEmprendedor INT,
    IN p_nombreApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_documentoApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
    IN p_apellidoApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_cargoApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_telefonoApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_celularApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_correoApoyo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_idTipoApoyo INT ,
    IN p_municipio VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_idMunicipio INT;
    DECLARE v_idEmpresa VARCHAR(255);

    -- Verificar si el documento de la empresa ya existe
    IF EXISTS (SELECT 1 FROM empresa WHERE documento = p_documentoEmpresa) THEN
        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "La empresa ya ha sido registrada";
    ELSE
        -- Obtener el ID del municipio
        SELECT id INTO v_idMunicipio FROM municipios WHERE nombre = p_municipio LIMIT 1;

        -- Insertar datos de la empresa
        INSERT INTO empresa (documento, nombre, cargo, razonSocial, url_pagina, telefono, celular, direccion, profesion, correo, experiencia, funciones, id_tipo_documento, id_municipio, id_emprendedor)
        VALUES (p_documentoEmpresa, p_nombreEmpresa, p_cargoEmpresa, p_razonSocial, p_urlPagina, p_telefonoEmpresa, p_celularEmpresa, p_direccionEmpresa, p_profesion, p_correoEmpresa, p_experiencia, p_funciones, p_idTipoDocumentoEmpresa, v_idMunicipio, p_idEmprendedor);

        -- Obtener el ID de la empresa recién insertada
        SET v_idEmpresa = p_documentoEmpresa;

        -- Insertar datos del apoyo asociado a la empresa si se proporcionan
        IF p_nombreApoyo IS NOT NULL THEN
            INSERT INTO apoyo_empresa (documento, nombre, apellido, cargo, telefono, celular, email, id_tipo_documento, id_empresa)
            VALUES (p_documentoApoyo, p_nombreApoyo, p_apellidoApoyo, p_cargoApoyo, p_telefonoApoyo, p_celularApoyo, p_correoApoyo, p_idTipoApoyo, v_idEmpresa);
        END IF;
    END IF;
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
