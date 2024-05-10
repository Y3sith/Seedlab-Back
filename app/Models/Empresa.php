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
        'urlPagina',
        'telefono',
        'celular',
        'direccion',
        'profesion',
        'correo',
        'experiencia',
        'funciones',
        'id_tipo_documento',
        'id_municipio',
        'id_emprendedor'
    ];


    public $timestamps = false;

    public function emprendedor(){
        return $this->belongsTo(Emprendedor::class, 'id_emprendedor');
    }

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function municipio(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function apoyoxempresa(){
        return $this->hasMany(ApoyoEmpresa::class, 'id_empresa');
    }

    public function respuestas(){
        return $this->hasMany(Respuesta::class, 'id_empresa');
    }

    public function puntajes(){
        return $this->hasMany(Puntaje::class, 'id_empresa');
    }
}
