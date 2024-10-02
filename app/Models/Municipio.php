<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Municipio
 *
 * @property int $id
 * @property string $nombre
 * @property int $id_departamento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Municipio extends Model
{
    protected $fillable = ['nombre', 'id_departamento'];

    public $timestamps = false;

    public function departamento()
    {
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Departamento.
        // Se establece una relación de muchos a uno, utilizando 'id_departamento' como clave foránea.
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function emprendedor(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Emprendedor.
        // Se establece una relación de uno a muchos, usando 'id_municipio' como clave foránea.
        return $this->hasMany(Emprendedor::class, 'id_municipio');
    }

    public function empresa(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Empresa.
        // Se establece una relación de uno a muchos, usando 'id_municipio' como clave foránea.
        return $this->hasMany(Empresa::class, 'id_municipio');
    }

    

}
