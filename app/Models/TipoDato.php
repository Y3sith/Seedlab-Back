<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDato extends Model
{
    use HasFactory;

    protected $table = 'tipo_dato';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    public function aliados(){
        // Establece una relación de uno a muchos con el modelo 'Aliado'.
        // Este modelo puede tener múltiples aliados asociados a través de la clave foránea 'id_tipo_dato'.
        return $this->hasMany(Aliado::class, 'id_tipo_dato');
    }

    public function actividades(){
        // Establece una relación de uno a muchos con el modelo 'Actividad'.
        // Este modelo puede tener múltiples actividades asociadas a través de la clave foránea 'id_tipo_dato'.
        return $this->hasMany(Actividad::class, 'id_tipo_dato');
    }
    
    public function contenidoLecciones(){
        // Establece una relación de uno a muchos con el modelo 'ContenidoLeccion'.
        // Este modelo puede tener múltiples contenidos de lecciones asociados a través de la clave foránea 'id_tipo_dato'.
        return $this->hasMany(ContenidoLeccion::class, 'id_tipo_dato');
    }

    

}
