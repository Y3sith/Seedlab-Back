<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'cargo',
        'razonSocial',
        'urlPagina',
        'telefono',
        'celular',
        'direccion',
        'profesion',
        'experiencia',
        'funciones',
        'id_municipio',
        'id_tipodocumento'
    ];

    public $timestamps = false;
}
