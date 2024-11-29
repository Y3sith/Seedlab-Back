<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RutaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RutaApiController extends Controller
{
    //Funcion para listar la ruta
    protected $rutaService;

    public function __construct(RutaService $rutaService)
    {
        $this->rutaService = $rutaService;
    }

    public function index(Request $request)
    {
        try {
            $estado = $request->input('estado', 'Activo'); // Valor predeterminado 'Activo'
            $rutas = $this->rutaService->obtenerRutas($estado);
            return response()->json($rutas);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los detalles de una ruta por su ID.
     * 
     * @param int $id - El ID de la ruta.
     * @return array - Datos de la ruta encontrada.
     */
    public function rutaxId($id)
    {
        try {
            $ruta = $this->rutaService->obtenerRutaPorId($id);
            return response()->json($ruta);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Almacena una nueva ruta en la base de datos.
     * 
     * @param Request $request - Objeto que contiene los datos de la solicitud.
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con mensaje de éxito o error.
     */
    public function store(Request $request)
    {
        try {
            return $this->rutaService->crearRuta($request->all());
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    //Función para buscar ruta por id, para mostrar todo el contenido de esa ruta
    public function rutaParaMostrarContenido()
    {
        try {
            $rutasActivas = $this->rutaService->obtenerRutasActivas();
            return response()->json($rutasActivas);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene todas las rutas activas.
     * 
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con la lista de rutas activas.
     */
    public function rutas()
    {
        return $this->rutaService->rutas();
    }


    /**
     * Actualiza una ruta específica en la base de datos.
     * 
     * @param Request $request - Objeto que contiene los datos de la solicitud.
     * @param int $id - El ID de la ruta a actualizar.
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con mensaje de éxito o error.
     */
    public function update(Request $request, $id)
    {
        $data = [
            'nombre' => $request->input('nombre'),
            'estado' => $request->input('estado'),
        ];

        $result = $this->rutaService->actualizarRuta($id, $data);

        return response()->json(['message' => $result['message']], $result['status']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->rutaService->desactivarRuta($id);

        return response()->json(['message' => $result['message']], $result['status']);
    }


    //Función para mostrar en la tabla actividades
    public function actnivleccontXruta($id, Request $request)
    {
        $estado = $request->input('estado', 'Activo');
        $result = $this->rutaService->obtenerActividadesPorRuta($id, $estado);

        if ($result['status'] === 200) {
            return response()->json($result['data']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }

    //Funcion para listar las actividades de los aliados
    public function actnividadxAliado($id, $id_aliado, Request $request)
    {
        $estado = $request->input('estado', 'Activo');
        $result = $this->rutaService->obtenerActividadesPorRutaYAliado($id, $id_aliado, $estado);

        if ($result['status'] === 200) {
            return response()->json($result['data']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }

    //Funcion para listar los niveles asignados a  los asesores
    public function actnividadxNivelAsesor($id, $id_asesor, Request $request)
    {
        $estado = $request->input('estado', 'Activo');
        $result = $this->rutaService->obtenerActividadesPorNivelYAsesor($id, $id_asesor, $estado);

        if ($result['status'] === 200) {
            return response()->json($result['data']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }

    //Funcion para listar toda la ruta para el emprendedor
    public function actividadCompletaxruta($id)
    {
        $result = $this->rutaService->obtenerRutaConActividades($id);

        if ($result['status'] === 200) {
            return response()->json($result['data']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function descargarArchivoContenido($contenidoId)
    {
        $result = $this->rutaService->descargarArchivoContenido($contenidoId);

        if (is_array($result)) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return $result;
    }

    public function getRutaInfo($id)
    {
        $result = $this->rutaService->obtenerRutaInfo($id);

        return response()->json($result['data'], $result['status']);
    }

    public function idRespuestas($idEmprendedor)
    {
        // Verificación de roles
        if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        $result = $this->rutaService->verificarRespuestasPorEmprendedor($idEmprendedor);

        if ($result['status'] !== 200) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result['data'], 200);
    }
}
