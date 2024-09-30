<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{

    use HasFactory;

    
    protected $fillable = ['name'];

  
    public $timestamps = false;

    public function users(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla User.
        // Se establece una relación de uno a muchos.
        return $this->hasMany(User::class);
    }
    public function municipios(){
        // Esta relación indica que el modelo actual puede tener múltiples registros en la tabla Municipio.
        // Se establece una relación de uno a muchos.
        return $this->hasMany(Municipio::class);
    }
}
