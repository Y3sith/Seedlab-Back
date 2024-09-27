<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoLeccion extends Model
{
    use HasFactory;

    protected $table = 'contenido_leccion';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fuente_contenido',
        'id_tipo_dato',
        'id_leccion',
    ];

    public $timestamps = false;


    
    public function tipoDato(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla TipoDato.
        // Se establece la conexión utilizando 'id_tipo_dato' como clave foránea.
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }

    public function leccion(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Leccion.
        // Se establece la conexión utilizando 'id_leccion' como clave foránea.
        return $this->belongsTo(Leccion::class, 'id_leccion');
    }
}
