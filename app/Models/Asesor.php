<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'celular',
        'id_autentication',
        'id_aliado'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(Autentication::class, 'id_autentication');
    }
    
    public function aliado(){
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }
}
