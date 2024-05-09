<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'seccion';

    protected $fillable = [
       'nombre'
    ];

    public function preguntas(){
        return $this->hasMany(Pregunta::class, 'id_seccion');
    }

    public $timestamps = false;
}
