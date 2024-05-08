<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    public $timestamps = false;

    public function emprendedor(){
        return $this->hasMany(Emprendedor::class, 'id_tipo_documento');
    }
}
