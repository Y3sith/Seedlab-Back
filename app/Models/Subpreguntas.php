<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subpreguntas extends Model
{
    use HasFactory;

    protected $fillable = [
        'texto',
        'puntaje',
        'id_pregunta',
    ];

    public $timestamps = false;
}
