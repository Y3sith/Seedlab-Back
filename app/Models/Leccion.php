<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'id_nivel'
    ];

    public $timestamps = false;

    public function nivel(){
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }

    public function contenidoLecciones(){
        return $this->hasMany(ContenidoLeccion::class, 'id_leccion');
    }
}
