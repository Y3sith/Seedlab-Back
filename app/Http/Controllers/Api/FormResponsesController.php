<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FormResponsesController extends Controller
{
    public function storeSection(Request $request, $sectionId, $id_empresa)
    {

        $key = "form:{$id_empresa}:section:{$sectionId}"; // Crea una clave única para cada sección del formulario

        // Guardar los datos de la sección en Redis con una caducidad de 5 días (432000 segundos)
        Redis::setex($key, 432000, $request->getContent());

        return response()->json(['message' => 'Sección guardada correctamente'], 200);
    }

    public function getAllRespuestasFromRedis($id_empresa)
    {
        // Buscar secciones en Redis
        $seccion1 = Redis::get("form:{$id_empresa}:section:1");
        $seccion2 = Redis::get("form:{$id_empresa}:section:2");
        $seccion3 = Redis::get("form:{$id_empresa}:section:3");
        $seccion4 = Redis::get("form:{$id_empresa}:section:4");
        $seccion5 = Redis::get("form:{$id_empresa}:section:5");

        // Verificar si al menos una de las secciones tiene datos
        if ($seccion1 || $seccion2 || $seccion3 || $seccion4 || $seccion5) {
            // Decodificar el JSON almacenado, si existe
            return response()->json([
                'seccion1' => $seccion1 ? json_decode($seccion1, true) : [],
                'seccion2' => $seccion2 ? json_decode($seccion2, true) : [],
                'seccion3' => $seccion3 ? json_decode($seccion3, true) : [],
                'seccion4' => $seccion4 ? json_decode($seccion4, true) : [],
                'seccion5' => $seccion5 ? json_decode($seccion5, true) : [],
            ]);
        } else {
            // Si no se encontraron datos, devolver un error
            return response()->json([
                'error' => 'No se encontraron datos para la empresa especificada.',
            ], 404); // Código de respuesta HTTP 404 - No encontrado
        }
    }

}
