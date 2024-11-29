<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'id_rol',
        'fecha_registro'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'temporary_password_created_at' => 'datetime',
        'password' => 'hashed',
    ];
    

    public function rol(){
        // Establece una relación de muchos a uno con el modelo 'Rol'.
        // Cada usuario puede tener un único rol asociado a través de la clave foránea 'id_rol'.
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function asesor(){
        // Establece una relación de uno a uno con el modelo 'Asesor'.
        // Cada usuario puede ser un único asesor a través de la clave foránea 'id_autentication'.
        return $this->hasOne(Asesor::class, 'id_autentication');
    }

    public function emprendedor(){
        // Establece una relación de uno a uno con el modelo 'Emprendedor'.
        // Cada usuario puede ser un único emprendedor a través de la clave foránea 'id_autentication'.
        return $this->hasOne(Emprendedor::class, 'id_autentication');
    }

    public function orientador(){
        // Establece una relación de uno a uno con el modelo 'Orientador'.
        // Cada usuario puede ser un único orientador a través de la clave foránea 'id_autentication'.
        return $this->hasOne(Orientador::class, 'id_autentication');
    }

    public function superAdmin(){
        // Establece una relación de uno a uno con el modelo 'SuperAdmin'.
        // Cada usuario puede ser un único superadministrador a través de la clave foránea 'id_autentication'.
        return $this->hasOne(SuperAdmin::class, 'id_autentication');
    }

    public function aliado(){
        // Establece una relación de uno a uno con el modelo 'Aliado'.
        // Cada usuario puede ser un único aliado a través de la clave foránea 'id_autentication'.
        return $this->hasOne(Aliado::class, 'id_autentication');
    }

    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }

    public $timestamps = false;
}
