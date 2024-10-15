<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividad';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fuente',
        'id_tipo_dato',
        'id_ruta',
        'id_aliado',
        'estado',
    ];

    protected $attributes = [
        'estado' => 1, // Valor predeterminado para estado
    ];
    

    public function tiposDatos(){
        // Esta relación indica que el modelo actual está asociado a un registro en la tabla TipoDato.
        // La columna 'id_tipo_dato' en este modelo actúa como clave foránea.
        return $this->belongsTo(TipoDato::class, 'id_tipo_dato');
    }
    
    public function aliado(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Aliado.
        // La columna 'id_aliado' en este modelo es la clave foránea que establece esta conexión.
        return $this->belongsTo(Aliado::class, 'id_aliado');
    }
    
    public function rutas(){
        // Esta relación muestra que el modelo actual está vinculado a un registro en la tabla Ruta.
        // La clave foránea en este caso es 'id_ruta'.
        return $this->belongsTo(Ruta::class, 'id_ruta');
    }
    
    public function nivel(){
        // Esta relación establece que el modelo actual puede tener múltiples registros en la tabla Nivel.
        // La clave foránea utilizada para esta relación es 'id_actividad'.
        return $this->hasMany(Nivel::class, 'id_actividad');
    }
    
    
    public $timestamps = false;
}
