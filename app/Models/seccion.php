<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class seccion extends Model
{
    use HasFactory;

    protected $table = 'seccion';

    protected $fillable = [
       'nombre',
    ];

    public function preguntas(){
        // Esta relación indica que el modelo actual puede tener múltiples preguntas asociadas.
        // Se establece una relación de uno a muchos, donde 'Preguntas' es el modelo relacionado
        // y 'id_seccion' es la clave foránea en el modelo de preguntas que se vincula a este modelo.
        return $this->hasMany(Preguntas::class, 'id_seccion');
    }

    public $timestamps = false;
}
