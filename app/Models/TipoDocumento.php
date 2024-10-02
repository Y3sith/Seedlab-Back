<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $table = 'tipo_documento';

    protected $fillable = [
        'nombre'
    ];

    public $timestamps = false;

    public function emprendedor(){
        // Establece una relación de uno a muchos con el modelo 'Emprendedor'.
        // Este modelo puede tener múltiples emprendedores asociados a través de la clave foránea 'id_tipo_documento'.
        return $this->hasMany(Emprendedor::class, 'id_tipo_documento');
    }
}
