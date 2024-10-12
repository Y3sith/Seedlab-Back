<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    
    protected $table = 'banner';

    protected $fillable = [
        'urlImagen', 
        'descripcion', 
        'estadobanner',
        'color',
        'id_aliado'
    ];

    public $timestamps = false;

    public function aliado(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Aliado.
        // Se establece la conexión utilizando 'id_aliado' como clave foránea.
        return $this->hasMany(Aliado::class, 'id_aliado');
    }

    public function getUrlImagenAttribute($value)
    {
        return $this->correctImageUrl($value);
    }

    protected function correctImageUrl($url)
    {
        // Verifica si la URL ya es completa (empieza con http o https)
        if (preg_match('/^(http|https):\/\//', $url)) {
            return $url;
        }

        // Verifica si la URL ya comienza con 'storage/'
        if (strpos($url, 'storage/') === 0 || strpos($url, '/storage/') === 0) {
            return asset(ltrim($url, '/'));
        }


        // Si no, elimina cualquier '/' inicial y agrega 'storage/'
        $url = ltrim($url, '/');

        return asset('storage/' . $url);
    }
}
