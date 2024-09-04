<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FormResponsesController extends Controller
{
    public function storeSection(Request $request, $sectionId)
    {
        $userId = auth()->id(); // Obtiene el ID del usuario autenticado
        $key = "form:{$userId}:section:{$sectionId}"; // Crea una clave única para cada sección del formulario
        
        // Guardar los datos de la sección en Redis con una caducidad de 5 días (432000 segundos)
        Redis::setex($key, 432000, $request->getContent());

        return response()->json(['message' => 'Sección guardada correctamente'], 200);
    }

    public function getSection($sectionId)
    {
        $userId = auth()->id(); // Obtiene el ID del usuario autenticado
        $key = "form:{$userId}:section:{$sectionId}"; // Crea una clave única para cada sección del formulario

        $sectionData = Redis::get($key); // Recupera los datos desde Redis

        if ($sectionData) {
            return response()->json(['data' => json_decode($sectionData)], 200);
        }

        return response()->json(['message' => 'No se encontraron datos para esta sección'], 404);
    }
}
