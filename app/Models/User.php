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
        'id_rol'
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
        'password' => 'hashed',
    ];
    

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

    public $timestamps = false;
}
