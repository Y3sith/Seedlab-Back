<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioAsesoria extends Model
{
    use HasFactory;

    protected $table = 'horarioasesoria';

    protected $fillable = [
        'observaciones',
        'fecha',
        'estado',
        'id_asesoria',
    ];

    public $timestamps = false;


    public function asesoria()
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Asesoria.
        // Se establece una relación de muchos a uno, usando 'id_asesoria' como clave foránea.
        return $this->belongsTo(Asesoria::class, 'id_asesoria');
    }

}
