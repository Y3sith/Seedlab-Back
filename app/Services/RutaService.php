<?php

namespace App\Services;

use App\Models\Ruta;
use App\Repositories\Ruta\RutaRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RutaService
{
    protected $rutaRepository;

    public function __construct(RutaRepositoryInterface $rutaRepository)
    {
        $this->rutaRepository = $rutaRepository;
    }

    public function obtenerRutas($estado)
    {
        // Verifica los permisos del usuario
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        // Convertir el estado a booleano
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        // Obtener rutas filtradas por estado usando el repositorio
        return $this->rutaRepository->obtenerRutasPorEstado($estadoBool);
    }

    public function obtenerRutaPorId($id)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = $this->rutaRepository->obtenerRutaPorId($id);
        if (!$ruta) {
            return response()->json(['error' => 'Ruta no encontrada'], 404);
        }

        return $ruta;
    }

    public function crearRuta($data)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $nombreRuta = $data['nombre'];

        if (strlen($nombreRuta) > 70) {
            return response()->json(['message' => 'El nombre de la ruta no puede tener más de 70 caracteres'], 422);
        }

        $existingRoute = $this->rutaRepository->obtenerRutaPorNombre($nombreRuta);
        if ($existingRoute) {
            return response()->json(['message' => 'El nombre de la ruta ya ha sido registrado anteriormente'], 422);
        }

        $ruta = $this->rutaRepository->crearRuta($data);
        return response()->json(["message" => "Ruta creada exitosamente", $ruta], 200);
    }

    public function obtenerRutasActivas()
    {
        if (!in_array(Auth::user()->id_rol, [1, 2, 3, 5])) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }

        return $this->rutaRepository->obtenerRutasActivas();
    }

    public function rutas()
    {
        if (!in_array(Auth::user()->id_rol, [1, 2, 3, 5])) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }

        return $this->rutaRepository->obtenerRutasActivas();
    }

    public function actualizarRuta($id, $data)
    {
        if (Auth::user()->id_rol != 1) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        $newNombre = $data['nombre'];

        if (strlen($newNombre) > 70) {
            return ['status' => 422, 'message' => 'El nombre de la ruta no puede tener más de 70 caracteres'];
        }

        $ruta = $this->rutaRepository->actualizarRuta($id, $data);

        if (!$ruta) {
            return ['status' => 404, 'message' => 'Ruta no encontrada'];
        }

        $existing = Ruta::where('nombre', $newNombre)->where('id', '!=', $id)->first();
        if ($existing) {
            return ['status' => 422, 'message' => 'El nombre ya ha sido registrado anteriormente'];
        }

        return ['status' => 200, 'message' => 'Ruta actualizada correctamente'];
    }

    public function desactivarRuta($id)
    {
        // Verificar permisos del usuario
        if (Auth::user()->id_rol != 1) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        // Desactivar la ruta mediante el repositorio
        $ruta = $this->rutaRepository->desactivarRuta($id);

        if (!$ruta) {
            return ['status' => 404, 'message' => 'Ruta no encontrada'];
        }

        return ['status' => 200, 'message' => 'Ruta desactivada exitosamente'];
    }

    public function obtenerActividadesPorRuta($idRuta, $estado)
    {
        // Verifica los permisos del usuario
        if (Auth::user()->id_rol != 1) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        // Convierte el estado a booleano
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        // Obtiene las actividades de la ruta
        $actividades = $this->rutaRepository->obtenerActividadesConEstado($idRuta, $estadoBool);

        if (is_null($actividades)) {
            return ['status' => 404, 'message' => 'Ruta no encontrada'];
        }

        return [
            'status' => 200,
            'data' => [
                'id' => $idRuta,
                'actividades' => $actividades
            ]
        ];
    }

    public function obtenerActividadesPorRutaYAliado($idRuta, $idAliado, $estado)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        $estadoBool = $estado === 'Activo' ? 1 : 0;

        $actividades = $this->rutaRepository->obtenerActividadesPorRutaYAliado($idRuta, $idAliado, $estadoBool);

        if (is_null($actividades)) {
            return ['status' => 404, 'message' => 'Ruta no encontrada'];
        }

        return [
            'status' => 200,
            'data' => [
                'id' => $idRuta,
                'actividades' => $actividades
            ]
        ];
    }

    public function obtenerActividadesPorNivelYAsesor($idRuta, $idAsesor, $estado)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        $estadoBool = $estado === 'Activo' ? 1 : 0;

        $actividades = $this->rutaRepository->obtenerActividadesPorNivelYAsesor($idRuta, $idAsesor, $estadoBool);

        if (is_null($actividades)) {
            return ['status' => 404, 'message' => 'Ruta no encontrada'];
        }

        return [
            'status' => 200,
            'data' => [
                'id' => $idRuta,
                'actividades' => $actividades
            ]
        ];
    }

    public function obtenerRutaConActividades($idRuta)
    {
        if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
            return ['status' => 401, 'message' => 'No tienes permisos para realizar esta acción'];
        }

        $ruta = $this->rutaRepository->obtenerRutaConActividades($idRuta);

        if (!$ruta || $ruta->actividades->isEmpty()) {
            return ['status' => 404, 'message' => 'No hay actividades disponibles para esta ruta'];
        }

        $ruta->actividades = $ruta->actividades->map(function ($actividad) {
            $actividad->id_asesor = $actividad->id_asesor ?? 'Ninguno';
            $actividad->estado = 'Activo';
            $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
            unset($actividad->aliado);
            return $actividad;
        });

        return ['status' => 200, 'data' => $ruta];
    }

    public function descargarArchivoContenido($contenidoId)
    {
        try {
            $contenidoLeccion = $this->rutaRepository->obtenerContenidoPorId($contenidoId);
            $fileName = $this->cleanFileName($contenidoLeccion->fuente_contenido);
            $filePath = 'documentos/' . $fileName;

            if (Storage::disk('public')->exists($filePath)) {
                $file = Storage::disk('public')->get($filePath);
                $fullPath = storage_path('app/public/' . $filePath);
                $type = mime_content_type($fullPath);
    

                return response($file, 200)
                    ->withHeaders([
                        'Content-Type' => $type,
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                        'Access-Control-Expose-Headers' => 'Content-Disposition',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, Content-Disposition',
                    ]);
            } else {
                return ['status' => 404, 'message' => 'Archivo no encontrado'];
            }
        } catch (Exception $e) {
            return ['status' => 500, 'message' => 'Error al intentar descargar el archivo: ' . $e->getMessage()];
        }
    }

    private function cleanFileName($filePath)
    {
        return Str::replaceFirst('/storage/documentos/', '', $filePath);
    }

    public function obtenerRutaInfo($id)
    {
        try {
            $ruta = $this->rutaRepository->obtenerRutaConRelaciones($id);

            if (!$ruta) {
                return ['status' => 404, 'data' => ['error' => 'Ruta no encontrada']];
            }

            $actividadesOptimizadas = $ruta->actividades->map(function ($actividad) {
                return [
                    'id' => $actividad->id,
                    'nombre' => $actividad->nombre,
                    'id_asesor' => $actividad->id_asesor ?? 'Ninguno',
                    'nombre_aliado' => $actividad->aliado->nombre ?? 'sin aliado',
                    'niveles' => $actividad->nivel->map(function ($nivel) {
                        return [
                            'id' => $nivel->id,
                            'nombre' => $nivel->nombre,
                            'lecciones' => $nivel->lecciones->map(function ($leccion) {
                                return [
                                    'id' => $leccion->id,
                                    'nombre' => $leccion->nombre,
                                    'contenidos' => $leccion->contenidoLecciones->map(function ($contenido) {
                                        return [
                                            'id' => $contenido->id,
                                            'nombre' => $contenido->titulo
                                        ];
                                    })
                                ];
                            })
                        ];
                    })
                ];
            });

            $ultimoElemento = [
                'nivel_id' => null,
                'leccion_id' => null,
                'contenido_id' => null
            ];

            foreach ($actividadesOptimizadas as $actividad) {
                foreach ($actividad['niveles'] as $nivel) {
                    $ultimoElemento['nivel_id'] = $nivel['id'];
                    foreach ($nivel['lecciones'] as $leccion) {
                        $ultimoElemento['leccion_id'] = $leccion['id'];
                        foreach ($leccion['contenidos'] as $contenido) {
                            $ultimoElemento['contenido_id'] = $contenido['id'];
                        }
                    }
                }
            }

            return ['status' => 200, 'data' => ['ultimo_elemento' => $ultimoElemento]];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()]];
        }
    }

    public function verificarRespuestasPorEmprendedor($idEmprendedor)
    {
        try {
            // Obtener las empresas asociadas al emprendedor
            $empresas = $this->rutaRepository->obtenerEmpresasPorEmprendedor($idEmprendedor);

            if ($empresas->isEmpty()) {
                return ['status' => 404, 'message' => 'No se encontraron empresas asociadas a este emprendedor'];
            }

            // Verificar si alguna de las empresas tiene al menos una respuesta
            $tieneRespuestas = $empresas->contains(function ($empresa) {
                return $empresa->respuestas()->exists();
            });

            return ['status' => 200, 'data' => $tieneRespuestas ? 1 : 0];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()];
        }
    }
}
