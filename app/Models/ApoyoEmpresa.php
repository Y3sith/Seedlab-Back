<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoyoEmpresa extends Model
{
    use HasFactory;

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

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public $timestamps = false;
}
