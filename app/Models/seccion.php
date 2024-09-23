<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class seccion extends Model
{
    use HasFactory;

    protected $table = 'seccion';

    protected $fillable = [
       'nombre',
    ];

    public function preguntas(){
        return $this->hasMany(Preguntas::class, 'id_seccion');
    }

    public $timestamps = false;
}
