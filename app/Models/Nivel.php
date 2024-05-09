<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_actividad'
    ];

    public $timestamps = false;

    public function actividad(){
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }

    public function niveles(){
        return $this->hasMany(Nivel::class, 'id_nivel');
    }
}
