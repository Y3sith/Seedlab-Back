<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    public function autenticacion(){
        // Esta relación indica que el modelo actual puede tener múltiples usuarios asociados.
        // Se establece una relación de uno a muchos, donde el modelo actual es el "padre" y 'User' es el "hijo".
        return $this->hasMany(User::class);
    }
 }
