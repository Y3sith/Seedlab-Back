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
        'estado',
        'color',
        'id_aliado'
    ];

    public $timestamps = false;

    public function aliado(){
        return $this->hasMany(Aliado::class, 'id_aliado');
    }
}
