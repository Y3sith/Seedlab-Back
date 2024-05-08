<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'ruta_multi',
        'id_tipo_dato',
        'id_asesor',
        'id_ruta',
    ];

    public $timestamps = false;
}
