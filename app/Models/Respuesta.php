<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'opcion',
        'texto_res',
        'valor',
        'id_pregunta',
        'id_emprendedor',
        'id_subpregunta',
    ];

    public $timestamps = false;


}
