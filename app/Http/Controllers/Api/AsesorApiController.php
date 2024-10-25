<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\AsesorService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AsesorApiController extends Controller
{
    protected $asesorService;

    //Inyección de service
    public function __construct(AsesorService $asesorService)
    {
        $this->asesorService = $asesorService;
    }

    /**
     * Almacenar un nuevo asesor en la base de datos.
     * 
     * @param Request $request - Los datos proporcionados en la solicitud para crear el asesor.
     * @return JsonResponse - Respuesta en formato JSON que contiene un mensaje de éxito o error.
     */
    public function store(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'Solo los aliados pueden crear asesores'], 403);
            }
            // Extrae los datos necesarios para la creación del asesor del request.
            $data = $request->only(['nombre', 'apellido', 'documento', 'celular', 'genero', 'direccion', 'aliado', 'id_tipo_documento', 'departamento', 'municipio', 'fecha_nac', 'email', 'estado']);
            
            // Genera una contraseña aleatoria para el asesor.
            $data['password'] = $this->generateRandomPassword();
            
            // Obtiene la imagen de perfil del request si se proporciona.
            $imagenPerfil = $request->file('imagen_perfil');

            //Log::info('Datos para crear asesor:', $data);

            // Llama al servicio para crear el asesor.
            $mensaje = $this->asesorService->crearAsesor($data, $imagenPerfil);

            //Log::info('Mensaje de resultado del servicio:', ['mensaje' => $mensaje]);

            // Retorna una respuesta exitosa en formato JSON con un mensaje.
            return response()->json(['message' => $mensaje], 201);
        } catch (Exception $e) {
            //Log::error('Error en AsesorApiController@store:', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Actualizar los datos de un asesor existente.
     * 
     * @param Request $request - Los datos para actualizar el asesor.
     * @param int $id - El ID del asesor a actualizar.
     * @return JsonResponse - Respuesta en formato JSON con el resultado de la actualización.
     */
    public function updateAsesor(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos para editar asesores'], 403);
            }

            // Extrae los datos necesarios para actualizar el asesor.
            $data = $request->only(['nombre', 'apellido', 'documento', 'celular', 'genero', 'direccion', 'id_tipo_documento', 'departamento', 'municipio', 'fecha_nac']);
            
            // Obtiene la nueva imagen de perfil, si se proporciona.
            $imagenPerfil = $request->file('imagen_perfil');

            // Llama al servicio para actualizar el asesor.
            $asesor = $this->asesorService->actualizarAsesor($id, $data, $imagenPerfil);

            // Retorna una respuesta exitosa con el asesor actualizado.
            return response()->json(['message' => 'Asesor actualizado correctamente', 'data' => $asesor], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un asesor por parte de un aliado.
     * 
     * @param Request $request - Los datos para actualizar el asesor.
     * @param int $id - El ID del asesor a actualizar.
     * @return JsonResponse - Respuesta en formato JSON con el mensaje del resultado.
     */
    public function updateAsesorxAliado(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'no tienes permiso para esta funcion'], 403);
            }

            // Obtiene todos los datos enviados en la solicitud.
            $data = $request->all();

             // Llama al servicio para actualizar el asesor.
            $message = $this->asesorService->updateAsesorxAliado($data, $id);

            // Retorna una respuesta exitosa con el mensaje del servicio.
            return response()->json(['message' => $message], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

     /**
     * Mostrar las asesorías de un asesor, filtrando por horario si es necesario.
     * 
     * @param int $id - El ID del asesor.
     * @param string $conHorario - Filtro para mostrar asesorías con o sin horario.
     * @return JsonResponse - Respuesta en formato JSON con las asesorías encontradas.
     */
    public function mostrarAsesoriasAsesor($id, $conHorario)
    {
        try {
            // Llama al servicio para obtener las asesorías filtradas por horario.
            $asesorias = $this->asesorService->obtenerAsesoriasPorId($id, $conHorario);
            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener el perfil de un asesor con información de su ubicación.
     * 
     * @param int $id - El ID del asesor.
     * @return JsonResponse - Respuesta en formato JSON con los datos del asesor.
     */
    public function userProfileAsesor($id)
    {
        try {

            // Llama al servicio para obtener el perfil del asesor con su ubicación.
            $asesor = $this->asesorService->obtenerAsesorConUbicacion($id);
            

            return response()->json($asesor, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

     /**
     * Genera una contraseña aleatoria para el asesor.
     * 
     * @param int $length - La longitud de la contraseña a generar (por defecto 8).
     * @return string - La contraseña generada aleatoriamente.
     */
    protected function generateRandomPassword(int $length = 8): string
    {
        // Genera y retorna una contraseña aleatoria de longitud específica.
        return Str::random($length);
    }
}
