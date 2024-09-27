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
        'id_departamento',
        'id_municipio'
    ];


    public function auth(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla User.
        // Se establece una relación de muchos a uno, utilizando 'id_autentication' como clave foránea.
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function municipios(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Municipio.
        // Se establece una relación de muchos a uno, usando 'id_municipio' como clave foránea.
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla TipoDocumento.
        // Se establece una relación de muchos a uno, utilizando 'id_tipo_documento' como clave foránea.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function departamento(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Departamento.
        // Se establece una relación de muchos a uno, usando 'id_departamento' como clave foránea.
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}
