<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoyoEmpresa extends Model
{
    use HasFactory;

    protected $table = 'apoyo_empresa';
    protected $primaryKey = 'documento';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'nombre',
        'apellido',
        'documento',
        'cargo',
        'telefono',
        'celular',
        'email',
        'id_tipo_documento',
        'id_empresa'
    ];

    public function tipoDocumento(){
        // Esta relación indica que el modelo actual está vinculado a un registro en la tabla TipoDocumento.
        // La columna 'id_tipo_documento' se utiliza como clave foránea para establecer esta conexión.
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
        
    public function empresa(){
        // Esta relación muestra que el modelo actual pertenece a un registro en la tabla Empresa.
        // La columna 'id_empresa' actúa como clave foránea, y 'documento' es la columna en la tabla Empresa que se utiliza para la referencia.
        return $this->belongsTo(Empresa::class, 'id_empresa', 'documento'); // Añadir la referencia a la columna 'documento'
    }

    public $timestamps = false;
}
