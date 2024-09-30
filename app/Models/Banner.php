<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    
    protected $table = 'banner';

    protected $fillable = [
        'urlImagen', 
        'descripcion', 
        'estadobanner',
        'color',
        'id_aliado'
    ];

    public $timestamps = false;

    public function aliado(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Aliado.
        // Se establece la conexión utilizando 'id_aliado' como clave foránea.
        return $this->hasMany(Aliado::class, 'id_aliado');
    }
}
