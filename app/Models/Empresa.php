<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';

    protected $primaryKey = 'documento';
    public $incrementing = false;

    protected $fillable = [
        'nombre',
        'documento',
        'cargo',
        'razonSocial',
        'url_pagina',
        'telefono',
        'celular',
        'direccion',
        'profesion',
        'correo',
        'fecha_registro',
        'experiencia',
        'funciones',
        'id_tipo_documento',
        'id_departamento',
        'id_municipio',
        'id_emprendedor'
    ];


    public $timestamps = false;

    public function emprendedor(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Emprendedor.
        // Se establece una relación de muchos a uno, usando 'id_emprendedor' como clave foránea.
        return $this->belongsTo(Emprendedor::class, 'id_emprendedor');
    }

    public function tipoDocumento(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla TipoDocumento.
        // Se establece una relación de muchos a uno, usando 'id_tipo_documento' como clave foránea.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function municipio(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Municipio.
        // Se establece una relación de muchos a uno, usando 'id_municipio' como clave foránea.
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function apoyoxempresa(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla ApoyoEmpresa.
        // Se establece una relación de uno a muchos, usando 'id_empresa' como clave foránea.
        return $this->hasMany(ApoyoEmpresa::class, 'id_empresa');
    }

    public function respuestas(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Respuesta.
        // Se establece una relación de uno a muchos, usando 'id_empresa' como clave foránea.
        return $this->hasMany(Respuesta::class, 'id_empresa');
    }

    public function puntajes(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Puntaje.
        // Se establece una relación de uno a muchos, usando 'id_empresa' como clave foránea.
        return $this->hasMany(Puntaje::class, 'id_empresa');
    }
}
