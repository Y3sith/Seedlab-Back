<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'superadmin';

    protected $fillable = [
        'nombre',
        'apellido',
        'imagen_perfil',
        'email',
        'direccion',
        'celular',
        'genero',
        'id_autentication'
    ];

    public $timestamps = false;

    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }

    public function personalizacionSistema(){
        return $this->hasMany(PersonalizacionSistema::class, 'id_super_admin');
    }
}
