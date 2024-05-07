<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'apellidos',
        'id_rol',
        'id_autentication'
    ];

    public $timestamps = false;
}
