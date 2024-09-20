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
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }
    
    public function lecciones(){
        return $this->hasMany(Leccion::class, 'id_nivel');
    }

    public function asesor(){
        return $this->belongsTo(Asesor::class, 'id_asesor');
    }
}
