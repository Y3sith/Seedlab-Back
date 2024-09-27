<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividad';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fuente',
        'id_tipo_dato',
        'id_ruta',
        'id_aliado',
        'estado',
    ];

    public function tiposDatos(){
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }
    public function aliado(){
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }
    
    public function rutas(){
        return $this->belongsTo(Ruta::class, 'id_ruta');
    }

    public function nivel(){
        return $this->hasMany(Nivel::class, 'id_actividad');
    }
    
    
    public $timestamps = false;
}
