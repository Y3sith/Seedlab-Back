<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autentication extends Model
{
    use HasFactory;


    protected $fillable = [
        'email', 
        'password',
        'id_rol'
    ];

    public $timestamps = false;

    public function rol(){
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function asesor(){
        return $this->hasOne(Asesor::class, 'id_autentication');
    }

    public function emprendedor(){
        return $this->hasOne(Emprendedor::class, 'id_autentication');
    }

    public function orientador(){
        return $this->hasOne(Orientador::class, 'id_autentication');
    }

    public function superAdmin(){
        return $this->hasOne(SuperAdmin::class, 'id_autentication');
    }

    public function aliado(){
        return $this->hasOne(Aliado::class, 'id_autentication');
    }
}
