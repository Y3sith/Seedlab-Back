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
        'urlpagina',
        'id_autentication',
        'id_tipo_dato'
    ];

    public $timestamps = false;

    public function auth()
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla User.
        // La columna 'id_autentication' actúa como clave foránea para establecer esta conexión.
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function tipoDato()
    {
        // Esta relación muestra que el modelo actual está vinculado a un registro en la tabla TipoDato.
        // La columna 'id_tipo_dato' es la clave foránea utilizada para esta relación.
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }

    public function asesor()
    {
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Asesor.
        // La columna 'id_aliado' actúa como clave foránea en esta conexión.
        return $this->hasMany(Asesor::class, 'id_aliado');
    }

    public function asesoria()
    {
        // Esta relación establece que el modelo actual puede tener múltiples registros en la tabla Asesoria.
        // La clave foránea utilizada para esta relación es 'id_aliado'.
        return $this->hasMany(Asesoria::class, 'id_aliado');
    }

    public function actividad()
    {
        // Esta relación indica que el modelo actual puede estar asociado a múltiples registros en la tabla Actividad.
        // La columna 'id_aliado' actúa como clave foránea para esta conexión.
        return $this->hasMany(Actividad::class, 'id_aliado');
    }

    public function banner()
    {
        // Esta relación muestra que el modelo actual pertenece a un registro en la tabla Banner.
        // La columna 'id_banner' es la clave foránea utilizada para establecer esta relación.
        return $this->belongsTo(Banner::class, 'id_banner');
    }

    // Accessor para 'logo'
    public function getLogoAttribute($value)
    {
        return $value ? $this->correctImageUrl($value) : null;
    }

    // Accessor para 'ruta_multi'
    public function getRutaMultiAttribute($value)
    {
        return $value ? $this->correctImageUrl($value) : null;
    }

    // Método para corregir la URL de la imagen
    protected function correctImageUrl($url)
    {
        // Verifica si la URL ya es completa (empieza con http o https)
        if (preg_match('/^(http|https):\/\//', $url)) {
            return $url;
        }

        // Verifica si la URL ya comienza con 'storage/'
        if (strpos($url, 'storage/') === 0) {
            return asset($url);
        }

        // Si no, elimina cualquier '/' inicial y agrega 'storage/'
        $url = ltrim($url, '/');

        return asset('storage/' . $url);
    }
}
