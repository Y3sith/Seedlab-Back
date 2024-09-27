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
        // Esta relación indica que el modelo actual pertenece a una sección específica.
        // Se establece una relación de muchos a uno, utilizando 'id_seccion' como clave foránea.
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function respuestas(){
        // Esta relación establece que el modelo actual puede tener múltiples respuestas asociadas.
        // Se establece una relación de uno a muchos, utilizando 'id_pregunta' como clave foránea en la tabla Respuesta.
        return $this->hasMany(Respuesta::class, 'id_pregunta');
    }

    public function subpreguntas(){
        // Esta relación indica que el modelo actual puede tener múltiples subpreguntas asociadas.
        // Se establece una relación de uno a muchos, utilizando 'id_pregunta' como clave foránea en la tabla Subpreguntas.
        return $this->hasMany(Subpreguntas::class, 'id_pregunta');
    }

    public $timestamps = false;
    
}
