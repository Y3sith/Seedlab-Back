<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliado extends Model
{
    use HasFactory;

    protected $table = 'aliado';

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'ruta_multi',
        'id_autentication',
        'id_tipo_dato'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function tipoDato(){
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }

    public function asesor(){
       return $this->hasMany(Asesor::class, 'id_aliado');
    }


}
