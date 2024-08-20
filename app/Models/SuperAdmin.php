<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'superadmin';

    protected $fillable = [
        'nombre',
        'apellido',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'id_autentication',
        'id_tipo_documento',
        'id_municipio'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function personalizacionSistema(){
        return $this->hasMany(PersonalizacionSistema::class, 'id_super_admin');
    }

    public function municipios(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
}
