<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalizacionSistema extends Model
{
    use HasFactory;

    protected $table = 'personalizacion_sistema';

    protected $fillable = [
        'imagen_logo',
        'nombre_sistema',
        'color_principal',
        'color_secundario',
        'descripcion_footer',
        'paginaWeb',
        'email',
        'telefono',
        'direccion',
        'ubicacion',
        'id_superadmin'
    ];

    public function superadmins(){
        // Esta relación indica que el modelo actual pertenece a un registro en la tabla Superadmin.
        // Se establece una relación de muchos a uno, utilizando 'id_superadmin' como clave foránea.
        return $this->belongsTo(Superadmin::class, 'id_superadmin');
    }

    public $timestamps = false;
}
