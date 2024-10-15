<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NivelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class NivelesController extends Controller
{
    protected $nivelService;

    public function __construct(NivelService $nivelService)
    {
        $this->nivelService = $nivelService;
    }

    /**
     * Crea un nuevo nivel en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['error' => 'No tienes permisos para crear niveles'], 401);
            }

            $data = $request->only(['nombre', 'id_asesor', 'id_actividad']);
            $nivel = $this->nivelService->crearNivel($data);

            return response()->json(['message' => 'Nivel creado con éxito', 'nivel' => $nivel], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Función para traer todos los niveles
    public function listarNiveles()
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['message' => 'No tienes permisos'], 401);
            }

            $niveles = $this->nivelService->listarNiveles();

            return response()->json($niveles);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Mostrar niveles asociados a una actividad
    public function NivelxActividad($id)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['message' => 'No tienes permisos'], 401);
            }

            $niveles = $this->nivelService->nivelesPorActividad($id);

            return response()->json($niveles);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //mostrar niveles asociados a una actividad por el id del asesor
    public function NivelxActividadxAsesor($idActividad, $idAsesor)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos'], 401);
            }

            $niveles = $this->nivelService->nivelesPorActividadYAsesor($idActividad, $idAsesor);

            if ($niveles->isEmpty()) {
                return response()->json(['error' => 'No se encontraron niveles para esta actividad y asesor'], 404);
            }

            return response()->json($niveles);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Función para editar el nivel
    public function editarNivel(Request $request, $id)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(['error' => 'No tienes permisos para editar niveles'], 401);
            }

            $data = $request->only(['nombre', 'id_asesor', 'id_actividad']);
            $this->nivelService->actualizarNivel($id, $data);

            return response()->json(["message" => "Nivel actualizado correctamente"], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
