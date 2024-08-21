<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientador extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'orientador';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'fecha_nac',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'id_autentication',
        'id_tipo_documento',
        'id_municipio'
    ];


    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function municipios(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
}
