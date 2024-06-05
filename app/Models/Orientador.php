<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientador extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'orientador';

    protected $fillable = [
        'nombre', 
        'apellido', 
        'celular', 
        'id_autentication'
    ];


    public function auth(){
        return $this->belongsTo(User::class, 'id_autentication');
    }
}
