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
        'ruta_multi',
        'id_tipo_dato',
        'id_asesor',
        'id_ruta',
    ];

    public function tiposDatos(){
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }

    public function asesor(){
        return $this->belongsTo(Asesor::class, 'id_asesor');
    }
    
    public function rutas(){
        return $this->belongsTo(Ruta::class, 'id_ruta');
    }
    
    
    public $timestamps = false;
}
