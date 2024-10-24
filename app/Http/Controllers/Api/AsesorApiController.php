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

    public function __construct(AsesorService $asesorService)
    {
        $this->asesorService = $asesorService;
    }

    public function store(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'Solo los aliados pueden crear asesores'], 403);
            }

            $data = $request->only(['nombre', 'apellido', 'documento', 'celular', 'genero', 'direccion', 'aliado', 'id_tipo_documento', 'departamento', 'municipio', 'fecha_nac', 'email', 'estado']);
            $data['password'] = $this->generateRandomPassword();
            $imagenPerfil = $request->file('imagen_perfil');

            Log::info('Datos para crear asesor:', $data);

            $mensaje = $this->asesorService->crearAsesor($data, $imagenPerfil);

            Log::info('Mensaje de resultado del servicio:', ['mensaje' => $mensaje]);

            return response()->json(['message' => $mensaje], 201);
        } catch (Exception $e) {
            Log::error('Error en AsesorApiController@store:', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function updateAsesor(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos para editar asesores'], 403);
            }

            $data = $request->only(['nombre', 'apellido', 'documento', 'celular', 'genero', 'direccion', 'id_tipo_documento', 'departamento', 'municipio', 'fecha_nac']);
            $imagenPerfil = $request->file('imagen_perfil');

            $asesor = $this->asesorService->actualizarAsesor($id, $data, $imagenPerfil);

            return response()->json(['message' => 'Asesor actualizado correctamente', 'data' => $asesor], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function updateAsesorxAliado(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'no tienes permiso para esta funcion'], 403);
            }

            $data = $request->all();
            $message = $this->asesorService->updateAsesorxAliado($data, $id);

            return response()->json(['message' => $message], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function mostrarAsesoriasAsesor($id, $conHorario)
    {
        try {
            $asesorias = $this->asesorService->obtenerAsesoriasPorId($id, $conHorario);
            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function userProfileAsesor($id)
    {
        try {
            $asesor = $this->asesorService->obtenerAsesorConUbicacion($id);
            // if ($asesor->imagen_perfil) {
            //     $asesor->imagen_perfil = url(str_replace('public', 'storage', $asesor->imagen_perfil));
            // }
            
            return response()->json($asesor, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    protected function generateRandomPassword(int $length = 8): string
    {
        return Str::random($length);
    }
}
