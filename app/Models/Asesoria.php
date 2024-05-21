<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesoria extends Model
{
    use HasFactory;

    protected $table = 'asesoria';

    protected $fillable = [
        'Nombre_sol',
        'notas',
        'isorientador',
        'asignacion',
        'fecha',
        'id_aliado',
        'doc_emprendedor',
    ];

    public $timestamps = false;

    public function aliado()
    {
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }

    public function emprendedor()
    {
        return $this->belongsTo(Emprendedor::class, 'doc_emprendedor', 'documento');
    }

    public function asesores()
    {
        return $this->belongsToMany(Aliado::class, 'asesoriaxasesor', 'id_asesoria', 'id_asesor');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioAsesoria::class, 'id_asesoria');
    }

    public function asesoriaxAsesor()
    {
        return $this->hasMany(AsesoriaxAsesor::class, 'id_asesoria');
    }
}
