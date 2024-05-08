<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoLeccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fuente',
        'id_tipo_dato',
        'id_leccion',
    ];

    public $timestamps = false;
}
