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
}
