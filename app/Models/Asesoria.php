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
        'id_orientador',
        'doc_emprendedor',
    ];

    public $timestamps = false;

    public function aliado()
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Aliado.
        // Utiliza 'id_aliado' como clave foránea para establecer la conexión.
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }
    
    public function emprendedor() 
    {
        // Esta relación muestra que el modelo actual está vinculado a un registro en la tabla Emprendedor.
        // Se utiliza 'doc_emprendedor' como clave foránea y 'documento' como clave local en Emprendedor.
        return $this->belongsTo(Emprendedor::class, 'doc_emprendedor', 'documento');
    }
    
    public function orientador()
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Orientador.
        // La columna 'id_orientador' se utiliza como clave foránea.
        return $this->belongsTo(Orientador::class, 'id_orientador');
    }
    
    public function asesores() 
    {
        // Esta relación muestra que el modelo actual está vinculado a múltiples Asesores.
        // Utiliza la tabla pivote 'asesoriaxasesor' con 'id_asesoria' y 'id_asesor' como claves foráneas.
        return $this->belongsToMany(Aliado::class, 'asesoriaxasesor', 'id_asesoria', 'id_asesor');
    }
    
    public function horarios() 
    {
        // Esta relación indica que el modelo actual tiene muchos Horarios de Asesoría.
        // La columna 'id_asesoria' en la tabla HorarioAsesoria se utiliza como clave foránea.
        return $this->hasMany(HorarioAsesoria::class, 'id_asesoria', 'id');
    }
    
    public function asesoriaxAsesor() 
    {
        // Esta relación muestra que el modelo actual tiene muchas entradas en la tabla AsesoriaxAsesor.
        // La columna 'id_asesoria' se utiliza como clave foránea.
        return $this->hasMany(AsesoriaxAsesor::class, 'id_asesoria');
    }    
}
