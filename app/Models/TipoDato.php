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
        return $this->hasMany(Aliado::class, 'id_tipo_dato');
    }

    public function actividades(){
        return $this->hasMany(Actividad::class, 'id_tipo_dato');
    }
    
    public function contenidoLecciones(){
        return $this->hasMany(ContenidoLeccion::class, 'id_tipo_dato');
    }

    

}
