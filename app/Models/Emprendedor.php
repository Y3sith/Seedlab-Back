<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emprendedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'celular',
        'genero',
        'fechaNacimiento',
        'id_rol',
        'id_municipio',
        'id_autentication'
    ];

    public $timestaps = false;
}
