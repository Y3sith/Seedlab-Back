<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoyoEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'cargo',
        'telefono',
        'celular',
        'email',
        'id_tipo_documento',
        'id_empresa'
    ];

    public $timestamps = false;
}
