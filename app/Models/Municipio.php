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
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function emprendedor(){
        return $this->hasMany(Emprendedor::class, 'id_municipio');
    }

    

}
