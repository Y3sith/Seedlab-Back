<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    use HasFactory;

    protected $table = 'asesor';

    protected $fillable = [
        'nombre',
        'apellido',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'id_autentication',
        'id_aliado'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication','id');
    }
    
    public function aliado(){
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }

    public function actividades(){
        return $this->hasMany(Actividad::class, 'id_asesor');
    }

    public function getNombresAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function asesorias() {
        return $this->belongsToMany(Asesoria::class, 'asesoriaxasesor', 'id_asesor', 'id_asesoria');
    }
}
