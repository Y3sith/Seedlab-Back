<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    use HasFactory;

    protected $table = 'pregunta';

    protected $fillable = [
        'texto',
        'puntaje',
        'id_seccion'
    ];

    public function seccion(){
        return $this->belongsTo(Secciones::class, 'id_seccion');
    }

    public function respuestas(){
        return $this->hasMany(Respuestas::class, 'id_pregunta');
    }

    public function subpreguntas(){
        return $this->hasMany(Subpreguntas::class, 'id_pregunta');
    }

    public $timestamps = false;
    
}
