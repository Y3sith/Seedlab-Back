<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subpreguntas extends Model
{
    use HasFactory;

    protected $table = 'subpregunta';

    protected $fillable = [
        'texto',
        'puntaje',
        'id_pregunta',
    ];

    public function preguntas() {
        // Esta relación indica que este modelo pertenece a una pregunta específica.
        // Se establece una relación de muchos a uno, donde 'Preguntas' es el modelo relacionado
        // y 'id_pregunta' es la clave foránea en este modelo que se vincula a la tabla de preguntas.
        return $this->belongsTo(Preguntas::class, 'id_pregunta');
    }
    
    public function respuestas() {
        // Esta relación indica que este modelo puede tener múltiples respuestas asociadas.
        // Se establece una relación de uno a muchos, donde 'Respuesta' es el modelo relacionado
        // y 'id_subpregunta' es la clave foránea en el modelo de respuestas que se vincula a este modelo.
        return $this->hasMany(Respuesta::class, 'id_subpregunta');
    }

    public $timestamps = false;
}
