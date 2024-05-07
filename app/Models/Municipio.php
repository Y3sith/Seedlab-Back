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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre', 'id_departamento'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'municipios';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the departamento that owns the municipio.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function users(){
        return $this->hasMany(User::class);
    }

}
