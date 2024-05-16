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
        return $this->belongsTo(Asesoria::class, 'id_asesoria');
    }

}
