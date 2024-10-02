<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsesoriaxAsesor extends Model
{
    use HasFactory;

    protected $table = 'asesoriaxasesor';

    protected $fillable = [
        'id_asesoria',
        'id_asesor',
    ];

    public $timestamps = false;


    public function asesoria() 
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Asesoria.
        // Utiliza 'id_asesoria' como clave foránea para establecer la conexión.
        return $this->belongsTo(Asesoria::class, 'id_asesoria');
    }
    
    public function asesor() 
    {
        // Esta relación muestra que el modelo actual está vinculado a un registro en la tabla Asesor.
        // La columna 'id_asesor' se utiliza como clave foránea.
        return $this->belongsTo(Asesor::class, 'id_asesor');
    }    

  

}
