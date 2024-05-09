<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoyoEmpresa extends Model
{
    use HasFactory;

    protected $table = 'apoyoEmpresa';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'cargo',
        'telefono',
        'celular',
        'email',
        'id_tipo_documento',
        'id_empresa'
    ];

    public function tipoDocumento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
   
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public $timestamps = false;
}
