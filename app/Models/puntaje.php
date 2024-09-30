<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class puntaje extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'puntaje';

    protected $fillable = [
        'info_general',
        'info_financiera',
        'info_mercado',
        'info_trl',
        'info_tecnica',
        'documento_empresa',
        'ver_form',
    ];

    public function empresas(){
        // Esta relación indica que el modelo actual pertenece a una empresa específica.
        // Se establece una relación de muchos a uno, utilizando 'id_empresa' como clave foránea.
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
