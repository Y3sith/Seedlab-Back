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
        'documento',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'fecha_nac',
        'id_autentication',
        'id_aliado',
        'id_tipo_documento',
        'id_departamento',
        'id_municipio'
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

    public function municipios(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}
