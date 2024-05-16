<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emprendedor extends Model
{
    use HasFactory;

    protected $table = 'emprendedor';

    protected $primaryKey = 'documento';
    public $incrementing = false;
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'celular',
        'genero',
        'fecha_nac',
        'direccion',
        'id_municipio',
        'id_autentication',
        'id_tipo_documento',
    ];

    public $timestamps = false;

    public function municipios(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication', 'id');
    }

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function empresas(){
        return $this->hasMany(Empresa::class, 'id_emprendedor', 'documento');
    }

    public function asesoria()
    {
        return $this->hasMany(Asesoria::class, 'doc_emprendedor', 'documento');
    }


    
}
