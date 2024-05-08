<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'razonSocial',
        'rutaMult',
        'id_autentication'
    ];

    public $timestaps = false;

    public function auth(){
        return $this->belongsTo(Autenticacion::class, 'id_autentication');
    }

}
