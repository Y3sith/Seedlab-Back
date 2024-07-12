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
        'banner',
        'ruta_multi',
        'id_autentication',
        'id_tipo_dato'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function tipoDato()
    {
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }

    public function asesor(){
       return $this->hasMany(Asesor::class, 'id_aliado');
    }

    public function asesoria()
    {
        return $this->hasMany(Asesoria::class, 'id_aliado');
    }

    public function actividad(){
        return $this->hasMany(Actividad::class, 'id_aliado');
    }

    public function banner(){
        return $this->belongsTo(Banner::class, 'id_banner');
    }

}
