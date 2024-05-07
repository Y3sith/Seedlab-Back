<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'descripcion',
        'logo',
        'razonSocial',
        'rutaMult',
        'id_rol',
        'id_autentication'
    ];

    public $timestaps = false;

}
