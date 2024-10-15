<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LeccionService;
use Exception;

class LeccionController extends Controller
{
    protected $leccionService;

    public function __construct(LeccionService $leccionService)
    {
        $this->leccionService = $leccionService;
    }

    /**
     * Crea una nueva lección en la base de datos.
     * Esta función permite a los asesores crear una lección asociada a un nivel específico.
     * Verifica los permisos del usuario y si ya existe una lección con el mismo nombre para el nivel proporcionado.
     */
    public function store(Request $request)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['error' => 'No tienes permisos para crear lecciones'], 401);
            }

            $data = [
                'nombre' => $request->nombre,
                'id_nivel' => $request->id_nivel,
            ];

            $leccion = $this->leccionService->crearLeccion($data);

            return response()->json(['message' => 'Lección creada con éxito', 'leccion' => $leccion], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Mostrar niveles asociados a una actividad
    public function LeccionxNivel($id)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['message' => 'No tienes permisos'], 401);
            }

            $lecciones = $this->leccionService->obtenerLeccionesPorNivel($id);

            return response()->json($lecciones);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza una lección existente en la base de datos.
     */
    public function editarLeccion(Request $request, $id)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['error' => 'No tienes permisos para editar lecciones'], 401);
            }

            $data = ['nombre' => $request->nombre];

            $this->leccionService->actualizarLeccion($id, $data);

            return response()->json(["message" => "Lección actualizada correctamente"], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
