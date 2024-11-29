<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActividadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ActividadController extends Controller
{
    protected $actividadService;

    // Inyecta el servicio de actividad para manejar la lógica de negocio relacionada con actividades.
    public function __construct(ActividadService $actividadService)
    {
        $this->actividadService = $actividadService;
    }

    //Obtiene todas las actividades
    public function index()
    {
        // Verifica los permisos del usuario autenticado para listar actividades.
        if (in_array(Auth::user()->id_rol, [3, 4, 5])) {
             // Llama al servicio para obtener todas las actividades.
            $actividades = $this->actividadService->listarTodas();
            return response()->json($actividades);
        }
        // Devuelve un error si el usuario no tiene permisos.
        return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
    }

    //Crea actividad
    public function store(Request $request)
    {
        try {
            // Verifica los permisos del usuario autenticado para crear una actividad.
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
            }

            // Valida los datos del request.
            $data = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                'id_ruta' => 'required|integer|exists:ruta,id',
                'id_aliado' => 'required|integer|exists:aliado,id',
                'fuente' => 'nullable',
            ]);

            // Llama al servicio para crear una nueva actividad.
            $actividad = $this->actividadService->crearActividad($data);
            return response()->json(['message' => 'Actividad creada con éxito', 'actividad' => $actividad], 201);
        } catch (Exception $e) {
            // Captura cualquier excepción y devuelve un mensaje de error.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Llama al servicio para obtener una actividad específica por su ID.
    public function show($id)
    {
        try {
            
            $actividad = $this->actividadService->obtenerPorId($id);
            if (!$actividad) {
                return response()->json(["error" => "Actividad no encontrada"], 404);
            }
            return response()->json($actividad);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Llama al servicio para actualizar la actividad.
    public function editarActividad(Request $request, $id)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 3, 4])) {
                return response()->json(["error" => "No tienes permisos para editar esta actividad"], 401);
            }

            // Valida los datos del request.
            $data = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                'id_aliado' => 'required|integer|exists:aliado,id',
                'estado' => 'required|boolean',
                'fuente' => 'nullable',
            ]);

            $actividad = $this->actividadService->actualizarActividad($id, $data);
            return response()->json(['message' => 'Actividad actualizada con éxito', 'actividad' => $actividad], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Llama al servicio para cambiar el estado de la actividad.
    public function Activar_Desactivar_Actividad($id)
    {
        try {
            // Verificar permisos del usuario
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permiso para desactivar la actividad'], 400);
            }

            // Cambiar el estado de la actividad mediante el servicio
            $actividad = $this->actividadService->activarDesactivarActividad($id);

            return response()->json(['message' => 'Estado de la Actividad actualizado correctamente', 'actividad' => $actividad]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    // Llama al servicio para obtener actividades asociadas a un aliado específico.
    public function verActividadAliado($idAliado)
    {
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
        }

        $actividades = $this->actividadService->obtenerActividadesPorAliado($idAliado);

        return response()->json($actividades);
    }

    // Llama al servicio para obtener la actividad con sus relaciones.
    public function actiNivelLeccionContenido($id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
            }

            $actividad = $this->actividadService->obtenerActividadConRelaciones($id);

            if (!$actividad) {
                return response()->json(['message' => 'Actividad no encontrada'], 404);
            }

            return response()->json($actividad);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    // Llama al servicio para obtener una actividad por su ID.
    public function actividadAsesor($id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 401);
            }

            $actividad = $this->actividadService->obtenerActividadPorId($id);

            if (!$actividad) {
                return response()->json(['message' => 'Actividad no encontrada'], 404);
            }

            return response()->json($actividad);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    // Llama al servicio para obtener los tipos de datos disponibles.
    public function tipoDato(){
        try {
            $tiposDatos = $this->actividadService->tipoDato();
            return response()->json($tiposDatos, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los tipos de datos: ' . $e->getMessage()], 500);
        }
    }
}
