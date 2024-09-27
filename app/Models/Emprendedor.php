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
        'imagen_perfil',
        'documento',
        'celular',
        'genero',
        'fecha_nac',
        'direccion',
        'id_emprendedor',
        'id_municipio',
        'id_autentication',
        'id_tipo_documento',
    ];

    public $timestamps = false;

    public function municipios(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Municipio.
        // Se establece una relación de muchos a uno, usando 'id_municipio' como clave foránea.
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function auth(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla User.
        // Se establece una relación de muchos a uno, usando 'id_autentication' como clave foránea y 'id' como clave local.
        return $this->belongsTo(User::class, 'id_autentication', 'id');
    }

    public function tipoDocumento(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla TipoDocumento.
        // Se establece una relación de muchos a uno, usando 'id_tipo_documento' como clave foránea.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function empresas(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Empresa.
        // Se establece una relación de uno a muchos, usando 'id_emprendedor' como clave foránea y 'documento' como clave local.
        return $this->hasMany(Empresa::class, 'id_emprendedor', 'documento');
    }

    public function asesoria()
    {
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Asesoria.
        // Se establece una relación de uno a muchos, usando 'doc_emprendedor' como clave foránea y 'documento' como clave local.
        return $this->hasMany(Asesoria::class, 'doc_emprendedor', 'documento');
    }
    
    public function getNombresAttribute()
    {
        // Este accesor devuelve el nombre completo concatenando 'nombre' y 'apellido'.
        return "{$this->nombre} {$this->apellido}";
    }

    
}
