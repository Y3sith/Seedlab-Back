<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContenidoLeccionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Contenido_por_LeccionController extends Controller
{

    protected $contenidoLeccionRepository;
    protected $imageService;

    protected $contenidoLeccionService;

    public function __construct(ContenidoLeccionService $contenidoLeccionService)
    {
        $this->contenidoLeccionService = $contenidoLeccionService;
    }


    /**
     * Crear contenido para una lección.
     * 
     * @param Request $request - La solicitud HTTP que contiene los datos.
     * @return JsonResponse - Mensaje de éxito o error con los datos creados.
     */
    public function store(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(["message" => "No tienes permisos para crear contenido"], 401);
            }

            // Obtiene los datos necesarios del request.
            $data = $request->only(['titulo', 'descripcion', 'id_tipo_dato', 'id_leccion', 'fuente_contenido']);
            $fuenteContenido = $request->file('fuente_contenido');

            // Crea el contenido usando el servicio.
            $contenido = $this->contenidoLeccionService->crearContenido($data, $fuenteContenido);

            return response()->json(['message' => 'Contenido de Lección creada con éxito ', 'data' => $contenido], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Editar el contenido de una lección.
     * 
     * @param Request $request - La solicitud HTTP con los datos actualizados.
     * @param int $id - El ID del contenido que se desea actualizar.
     * @return JsonResponse - Mensaje de éxito o error con los datos actualizados.
     */
    public function editarContenidoLeccion(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(["message" => "No tienes permisos para editar contenido"], 401);
            }

            // Obtiene los datos necesarios del request.
            $data = $request->only(['titulo', 'descripcion', 'id_leccion', 'id_tipo_dato']);
            $fuenteContenido = $request->file('fuente_contenido');

            // Llama al servicio para editar el contenido.
            $resultado = $this->contenidoLeccionService->editarContenido($id, $data, $fuenteContenido);

            return response()->json(["message" => "Contenido actualizado correctamente", "data" => $resultado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener los tipos de datos disponibles para los contenidos.
     * 
     * @return JsonResponse - Lista de tipos de datos.
     */

    public function tipoDatoContenido()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
        }
        // Obtiene los tipos de datos a través del servicio.
        $tiposDeDato = $this->contenidoLeccionService->obtenerTiposDeDato();

        return response()->json($tiposDeDato);
    }

     /**
     * Ver el contenido asociado a una lección específica.
     * 
     * @param int $id - El ID de la lección.
     * @return JsonResponse - Lista de contenidos asociados a la lección.
     */
    public function verContenidoPorLeccion($id)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
        }

        // Obtiene el contenido de la lección a través del servicio.
        $contenido = $this->contenidoLeccionService->obtenerContenidoPorLeccion($id);

        return response()->json($contenido);
    }
}
