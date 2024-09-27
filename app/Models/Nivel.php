<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'nivel';

    protected $fillable = [
        'nombre',
        'id_asesor',
        'id_actividad'
    ];
    public $timestamps = false;
    public function actividad(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Actividad.
        // Se establece una relación de muchos a uno, utilizando 'id_actividad' como clave foránea.
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }
    
    public function lecciones(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Leccion.
        // Se establece una relación de uno a muchos, usando 'id_nivel' como clave foránea.
        return $this->hasMany(Leccion::class, 'id_nivel');
    }

    public function asesor(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Asesor.
        // Se establece una relación de muchos a uno, utilizando 'id_asesor' como clave foránea.
        return $this->belongsTo(Asesor::class, 'id_asesor');
    }
}
