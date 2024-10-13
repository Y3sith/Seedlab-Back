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
     * Store a newly created resource in storage.
     */  public function store(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(["message" => "No tienes permisos para crear contenido"], 401);
            }

            $data = $request->only(['titulo', 'descripcion', 'id_tipo_dato', 'id_leccion', 'fuente_contenido']);
            $fuenteContenido = $request->file('fuente_contenido');

            $contenido = $this->contenidoLeccionService->crearContenido($data, $fuenteContenido);

            return response()->json(['message' => 'Contenido de Lección creada con éxito ', 'data' => $contenido], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function editarContenidoLeccion(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(["message" => "No tienes permisos para editar contenido"], 401);
            }

            $data = $request->only(['titulo', 'descripcion', 'id_leccion', 'id_tipo_dato']);
            $fuenteContenido = $request->file('fuente_contenido');

            $resultado = $this->contenidoLeccionService->editarContenido($id, $data, $fuenteContenido);

            return response()->json(["message" => "Contenido actualizado correctamente", "data" => $resultado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    public function tipoDatoContenido()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
        }

        $tiposDeDato = $this->contenidoLeccionService->obtenerTiposDeDato();

        return response()->json($tiposDeDato);
    }

    public function verContenidoPorLeccion($id)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
        }

        $contenido = $this->contenidoLeccionService->obtenerContenidoPorLeccion($id);

        return response()->json($contenido);
    }
}
