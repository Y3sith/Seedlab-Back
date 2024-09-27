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

    public $timestamps = false;

    public function auth(){
        // Establece una relación de pertenencia a un usuario específico.
        // Este modelo se vincula al modelo 'User' a través de la clave foránea 'id_autentication'.
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function personalizacionSistema(){
        // Establece una relación de uno a muchos con el modelo 'PersonalizacionSistema'.
        // Este modelo puede tener múltiples personalizaciones asociadas a través de la clave foránea 'id_super_admin'.
        return $this->hasMany(PersonalizacionSistema::class, 'id_super_admin');
    }

    public function municipios(){
        // Establece una relación de pertenencia a un municipio específico.
        // Este modelo se vincula al modelo 'Municipio' a través de la clave foránea 'id_municipio'.
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        // Establece una relación de pertenencia a un tipo de documento específico.
        // Este modelo se vincula al modelo 'TipoDocumento' a través de la clave foránea 'id_tipo_documento'.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
}
