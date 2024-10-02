<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    use HasFactory;

    protected $table = 'asesor';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'fecha_nac',
        'id_autentication',
        'id_aliado',
        'id_tipo_documento',
        'id_municipio'
    ];

    public $timestamps = false;

    public function auth(){
        // Esta relación indica que el modelo actual está vinculado a un registro en la tabla User.
        // Utiliza 'id_autentication' como clave foránea en el modelo actual y 'id' como clave local en la tabla User.
        return $this->belongsTo(User::class, 'id_autentication', 'id');
    }
    
    public function aliado(){
        // Esta relación muestra que el modelo actual pertenece a un registro en la tabla Aliado.
        // La columna 'id_aliado' se utiliza como clave foránea para establecer esta conexión.
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }

    public function actividades(){
        // Esta relación indica que el modelo actual tiene muchas Actividades.
        // La columna 'id_asesor' en la tabla Actividad se utiliza como clave foránea.
        return $this->hasMany(Actividad::class, 'id_asesor');
    }

    public function getNombresAttribute()
    {
        // Este accesor devuelve una cadena que combina el nombre y apellido del modelo.
        return "{$this->nombre} {$this->apellido}";
    }

    public function asesorias() {
        // Esta relación indica que el modelo actual está vinculado a múltiples Asesorías.
        // Utiliza la tabla pivote 'asesoriaxasesor' con 'id_asesor' y 'id_asesoria' como claves foráneas.
        return $this->belongsToMany(Asesoria::class, 'asesoriaxasesor', 'id_asesor', 'id_asesoria');
    }

    public function municipios(){
        // Esta relación muestra que el modelo actual pertenece a un registro en la tabla Municipio.
        // La columna 'id_municipio' se utiliza como clave foránea.
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function tipoDocumento(){
        // Esta relación indica que el modelo actual está vinculado a un registro en la tabla TipoDocumento.
        // La columna 'id_tipo_documento' se utiliza como clave foránea.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
}
