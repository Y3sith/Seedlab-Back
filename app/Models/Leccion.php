<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leccion extends Model
{
    use HasFactory;

    protected $table = 'leccion';

    protected $fillable = [
        'nombre',
        'id_nivel'
    ];

    public $timestamps = false;

    public function nivel(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Nivel.
        // Se establece una relación de muchos a uno, utilizando 'id_nivel' como clave foránea.
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }

    public function contenidoLecciones(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla ContenidoLeccion.
        // Se establece una relación de uno a muchos, usando 'id_leccion' como clave foránea.
        return $this->hasMany(ContenidoLeccion::class, 'id_leccion');
    }
}
