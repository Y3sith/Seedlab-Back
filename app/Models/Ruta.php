<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'ruta';

    protected $fillable = [
        'nombre',
        'fecha_creacion',
        'estado',
    ];

    public $timestamps = false;

    public function actividades(){
        // Esta relación indica que el modelo actual puede tener múltiples actividades asociadas.
        // Se establece una relación de uno a muchos, donde 'Actividad' es el modelo relacionado
        // y 'id_ruta' es la clave foránea en el modelo de actividades que se vincula a este modelo.
        return $this->hasMany(Actividad::class, 'id_ruta');
    }
}
