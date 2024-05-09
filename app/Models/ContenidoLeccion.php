<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoLeccion extends Model
{
    use HasFactory;

    protected $table = 'contenidoLeccion';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fuente',
        'id_tipo_dato',
        'id_leccion',
    ];

    public $timestamps = false;

    public function leccion(){
        return $this->belongsTo(Leccion::class, 'id_leccion');
    }
    
    public function tipoDato(){
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }
}
