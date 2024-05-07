<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_de_documento'
    ];

    public $timestamps = false;

    public function users(){
        return $this->hasMany(User::class, 'id_tipo_documento');
    }
}
