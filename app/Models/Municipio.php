<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'id_departamento'];

    public $timestamps = false;


    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
