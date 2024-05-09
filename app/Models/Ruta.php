<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function actividades(){
        return $this->hasMany(Actividad::class, 'id_ruta');
    }
}
