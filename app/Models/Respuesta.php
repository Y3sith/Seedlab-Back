<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $table = 'respuesta';

    protected $fillable = [
        'opcion',
        'texto_res',
        'valor',
        'id_pregunta',
        'id_empresa',
        'id_subpregunta',
    ];

    public function preguntas(){
        // Esta relación indica que el modelo actual pertenece a una pregunta específica.
        // Se establece una relación de muchos a uno, utilizando 'id_pregunta' como clave foránea.
        return $this->belongsTo(Preguntas::class, 'id_pregunta');
    }

    public function subpreguntas(){
        // Esta relación indica que el modelo actual pertenece a una subpregunta específica.
        // Se establece una relación de muchos a uno, utilizando 'id_subpregunta' como clave foránea.
        return $this->belongsTo(Subpreguntas::class, 'id_subpregunta');
    }
    
    public function empresas(){
        // Esta relación indica que el modelo actual pertenece a una empresa específica.
        // Se establece una relación de muchos a uno, utilizando 'id_empresa' como clave foránea.
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public $timestamps = false;


}
