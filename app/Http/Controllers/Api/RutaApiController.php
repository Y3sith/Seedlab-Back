<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ContenidoLeccion;
use App\Models\Empresa;
use App\Models\Respuesta;
use App\Models\Ruta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class RutaApiController extends Controller
{
    /**
     * Muestra una lista de recursos de la ruta, filtrada por estado.
     * 
     * @param Request $request - Objeto que contiene los datos de la solicitud.
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con la lista de rutas filtrada.
     */
    public function index(Request $request)
    {
        try {
            // Verifica si el usuario tiene permisos (roles 1, 3 o 4) para acceder a la funcionalidad
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                // Retorna un error 401 si el usuario no tiene los permisos adecuados
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $estado = $request->input('estado', 'Activo'); // Obtener el estado desde el request, por defecto 'Activo'

            // Convierte el estado a un valor booleano, 1 para 'Activo' y 0 para 'Inactivo'
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            // Obtiene las rutas con el estado solicitado, filtrando por el campo 'estado'
            $rutaVer = Ruta::where('estado', $estadoBool)
                ->get(['id', 'nombre', 'fecha_creacion', 'estado']);

            // Formatea las rutas obtenidas para incluir el estado en formato legible
            $rutasi = $rutaVer->map(function ($rutaVers) {
                return [
                    'id' => $rutaVers->id,
                    'nombre' => $rutaVers->nombre,
                    'fecha_creacion' => $rutaVers->fecha_creacion,
                    'estado' => $rutaVers->estado == 1 ? 'Activo' : 'Inactivo',
                ];
            });

            // Retorna la lista de rutas formateada como respuesta en formato JSON
            return response()->json($rutasi);
        } catch (Exception $e) {
            // Si ocurre un error, captura la excepción y retorna un mensaje de error con código 500
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
            // Verifica si el usuario tiene permisos (roles 1 o 3) para acceder a la funcionalidad
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                // Retorna un error 401 si el usuario no tiene los permisos adecuados
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Busca la ruta por su ID y selecciona los campos relevantes
            $ruta = Ruta::where('id', $id)
                ->select('id', 'nombre', 'fecha_creacion', 'estado')
                ->first();

            // Retorna los detalles de la ruta formateada
            return [
                'id' => $ruta->id,
                'nombre' => $ruta->nombre,
                'fecha_creacion' => $ruta->fecha_creacion,
                'estado' => $ruta->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            // Si ocurre un error, captura la excepción y retorna un mensaje de error con código 500
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
            // Verifica si el usuario tiene permisos (solo el rol 1) para crear una ruta
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene el nombre de la ruta desde el request
            $nombreRuta = $request->nombre;

            // Valida que el nombre no exceda los 70 caracteres
            if (strlen($nombreRuta) > 70) {
                return response()->json(['message' => 'El nombre de la ruta no puede tener más de 70 caracteres'], 422);
            }

            // Verifica si ya existe una ruta con el mismo nombre
            $existingRoute = Ruta::where('nombre', $request->nombre)->first();
            if ($existingRoute) {
                return response()->json(['message' => 'El nombre de la ruta ya ha sido registrado anteriormente'], 422);
            }

            // Crea la nueva ruta en la base de datos
            $ruta = Ruta::create([
                "nombre" => $nombreRuta,
                "fecha_creacion" => Carbon::now(),
                "estado" => 1, // Estado por defecto: activo
            ]);

            // Retorna un mensaje de éxito con los datos de la ruta creada
            return response()->json(["message" => "Ruta creada exitosamente", $ruta], 200);
        } catch (Exception $e) {
            // Si ocurre un error, captura la excepción y retorna un mensaje de error con código 500
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
        // Verifica si el usuario tiene permisos para listar rutas (roles 1, 2, 3 o 5)
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 5 && Auth::user()->id_rol != 2) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }

        // Obtiene las rutas activas
        $rutasActivas = Ruta::where('estado', 1)->get();

        // Retorna las rutas activas en formato JSON
        return response()->json($rutasActivas);
    }

    public function rutasmejorado()
    {
        if (!in_array(Auth::user()->id_rol, [1, 2, 3, 5])) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }
    
        $rutasActivas = Ruta::where('estado', 1)->select('id')->get();
    
        return response()->json($rutasActivas);
    }

    /**
     * Obtiene todas las rutas activas con sus actividades, niveles, lecciones y contenido.
     * 
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con las rutas activas y su contenido.
     */
    public function rutasActivas()
    {
        // Verifica si el usuario tiene permisos para listar rutas con contenido (roles 1, 2, 3 o 5)
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 5 && Auth::user()->id_rol != 2) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }

        // Obtiene las rutas activas junto con sus actividades, niveles, lecciones y contenido
        $rutasActivas = Ruta::where('estado', 1)->with('actividades.nivel.lecciones.contenidoLecciones')->get();

        // Retorna las rutas activas con todas las relaciones cargadas en formato JSON
        return response()->json($rutasActivas);
    }

    /**
     * Muestra los detalles completos de una ruta con todas sus actividades, niveles, lecciones y contenido.
     * 
     * @param int $id - El ID de la ruta.
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con los detalles de la ruta.
     */
    public function mostrarRutaConContenido($id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        // Obtener la ruta por su ID con las actividades y sus niveles, lecciones y contenido por lección

        $ruta = Ruta::with('actividades.nivel.lecciones.contenidoLecciones')->get();
        // Retornar la ruta con todas las relaciones cargadas
        return response()->json($ruta);
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
        // Verifica si el usuario tiene permisos (solo el rol 1) para actualizar la ruta
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $ruta = Ruta::find($id);

            $newNombre = $request->input('nombre');
            // Valida que el nombre de la ruta no exceda los 70 caracteres
            if (strlen($newNombre) > 70) {
                return response()->json(['message' => 'El nombre de la ruta no puede tener más de 70 caracteres'], 422);
            }

            //busca que el nombre no est'e repetido
            $newNombre = $request->input('nombre');
            if ($newNombre && $newNombre !== $ruta->nombre) {
                $existing = Ruta::where('nombre', $newNombre)->where('id', '!=', $id)->first();
                if ($existing) {
                    return response()->json(['message' => 'El nombre ya ha sido registrado anteriormente'], 422);
                }
            }
            if (!$ruta) {
                return response()->json([
                    'message' => 'Ruta no encontrada'
                ], 404);
            }
            $ruta->update([
                'nombre' => $newNombre,
                'estado' => $request->estado,
            ]);

            //return response()->json(['message' => 'Ruta actualizada correctamente', $ruta], 200); //mostrar ruta al actualizar
            return response()->json(['message' => 'ruta actualizada correctamente'], 200); //mostrar solo el mensaje
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Verifica que el usuario tenga el rol adecuado para ejecutar esta acción (rol 1).
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        // Busca la ruta a través del ID proporcionado.
        $ruta = Ruta::find($id);

        // Si no se encuentra la ruta, devuelve un error 404.
        if (!$ruta) {
            return response()->json([
                'message' => 'Ruta no encontrada'
            ], 404);
        }

        // Cambia el estado de la ruta a 0 (desactivada).
        $ruta->update([
            'estado' => 0,
        ]);

        // Devuelve un mensaje indicando que la ruta fue desactivada exitosamente.
        return response()->json([
            'message' => 'Ruta desactivada exitosamente'
        ], 200);
    }


    public function actnivleccontXruta($id, Request $request)
    {
        try {
            // Verifica que el usuario tenga los permisos adecuados (solo rol 1).
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene el estado de la ruta desde la solicitud, por defecto 'Activo'.
            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            // Busca la ruta por su ID.
            $ruta = Ruta::where('id', $id)->first();

            // Si no se encuentra la ruta, devuelve un error 404.
            if (!$ruta) {
                return response()->json(['error' => 'Ruta no encontrada'], 404);
            }

            // Busca las actividades asociadas a la ruta con el estado correspondiente.
            $actividades = $ruta->actividades()
                ->where('estado', $estadoBool)
                ->select('id', 'nombre', 'id_ruta', 'estado', 'id_aliado')
                ->with(['aliado:id,nombre'])
                ->get()
                ->map(function ($actividad) {
                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'id_ruta' => $actividad->id_ruta,
                        'estado' => $actividad->estado == 1 ? 'Activo' : 'Inactivo',
                        'id_aliado' => $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado'
                    ];
                });

            // Devuelve la ruta con sus actividades en formato JSON.
            return response()->json([
                'id' => $ruta->id,
                'actividades' => $actividades
            ]);
        } catch (Exception $e) {
            // Devuelve un error si algo sale mal en el proceso.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function actnividadxAliado($id, $id_aliado, Request $request)
    {
        try {
            // Verifica que el usuario tenga los permisos adecuados (roles 1 o 3).
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene el estado de la actividad desde la solicitud, por defecto 'Activo'.
            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            // Busca la ruta por su ID.
            $ruta = Ruta::where('id', $id)->first();

            // Si no se encuentra la ruta, devuelve un error 404.
            if (!$ruta) {
                return response()->json(['error' => 'Ruta no encontrada'], 404);
            }

            // Busca las actividades de la ruta filtradas por estado y por el ID del aliado.
            $actividades = $ruta->actividades()
                ->where('estado', $estadoBool)
                ->where('id_aliado', $id_aliado)
                ->select('id', 'nombre', 'id_ruta', 'estado', 'id_aliado')
                ->with([
                    'aliado:id,nombre',
                    'nivel:id,id_asesor,id_actividad',
                    'nivel.asesor:id,nombre'
                ])
                ->get()
                ->map(function ($actividad) {
                    // Obtiene el primer asesor relacionado con la actividad, si existe.
                    $primerAsesor = $actividad->nivel->map(function ($nivel) {
                        return $nivel->asesor ? $nivel->asesor->nombre : 'Ninguno';
                    })->first() ?? 'Ninguno';
                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'id_ruta' => $actividad->id_ruta,
                        'estado' => $actividad->estado == 1 ? 'Activo' : 'Inactivo',
                        'id_aliado' => $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado',
                        'id_asesor' => $primerAsesor
                    ];
                });

            // Devuelve la ruta y sus actividades filtradas en formato JSON.
            return response()->json([
                'id' => $ruta->id,
                'actividades' => $actividades
            ]);
        } catch (Exception $e) {
            // Devuelve un error si algo sale mal en el proceso.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function actnividadxNivelAsesor($id, $id_asesor, Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            $ruta = Ruta::where('id', $id)
                ->with(['actividades' => function ($query) use ($estadoBool, $id_asesor) {
                    $query->where('estado', $estadoBool)
                        ->whereHas('nivel', function ($nivelQuery) use ($id_asesor) {
                            $nivelQuery->where('id_asesor', $id_asesor);
                        })
                        ->with(['aliado:id,nombre', 'nivel.asesor:id,nombre']);
                }])
                ->first();

            // Verificar si se encontró la ruta
            if (!$ruta) {
                return response()->json(['error' => 'Ruta no encontrada'], 404);
            }

            // Formatear la respuesta
            $respuesta = [
                'id' => $ruta->id,
                'actividades' => $ruta->actividades->map(function ($actividad) use ($id_asesor) {
                    // Filtrar el nivel exacto que tenga el id_asesor buscado
                    $asesorNombre = $actividad->nivel->firstWhere('id_asesor', $id_asesor)?->asesor->nombre ?? 'Ninguno';

                    return [
                        'id' => $actividad->id,
                        'nombre' => $actividad->nombre,
                        'estado' => $actividad->estado ? 'Activo' : 'Inactivo',
                        'id_aliado' => $actividad->aliado->nombre ?? 'Sin aliado',
                        'id_asesor' => $asesorNombre,
                    ];
                }),
            ];

            // Retornar la respuesta en formato JSON
            return response()->json($respuesta);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function actividadCompletaxruta($id)
    {
        try {
            // Verificar si el usuario tiene uno de los roles permitidos (1, 5, 2), de lo contrario, devolver error 401 (No autorizado)
            if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
    

            // Obtener la ruta con sus actividades activas (estado 1), niveles, lecciones, contenido de las lecciones y aliado
            $ruta = Ruta::where('id', $id)
                ->with(['actividades' => function ($query) {
                    $query->where('estado', 1);
                }, 'actividades.nivel.lecciones.contenidoLecciones', 'actividades.aliado'])
                ->get();

            // Verificar si la ruta o las actividades están vacías, en cuyo caso devolver un mensaje de que no hay actividades disponibles
            if ($ruta->isEmpty() || $ruta->first()->actividades->isEmpty()) {
                return response()->json(['mensaje' => 'No hay actividades disponibles para esta ruta'], 404);
            }

            // Formatear la respuesta de las actividades, asignar estado como 'Activo' y definir el aliado o 'Sin aliado'
            $ruta = $ruta->map(function ($r) {
                $r->actividades = $r->actividades->map(function ($actividad) {
                    $actividad->id_asesor = $actividad->id_asesor ?? 'Ninguno';
                    $actividad->estado = 'Activo';
                    $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
                    unset($actividad->aliado); // Eliminar el campo aliado del objeto actividad
                    return $actividad;
                });
                return $r;
            });

            // Devolver la ruta y sus actividades en formato JSON
            return response()->json($ruta);
        } catch (Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error con código 500 (Error interno)
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function descargarArchivoContenido($contenidoId)
    {
        try {
            // Buscar el contenido de la lección por ID
            $contenidoLeccion = ContenidoLeccion::findOrFail($contenidoId);
            // Limpiar el nombre del archivo para quitar la ruta /storage/documentos/
            $fileName = $this->cleanFileName($contenidoLeccion->fuente_contenido);
            $filePath = 'documentos/' . $fileName; // Definir la ruta del archivo

            // Verificar si el archivo existe en el disco
            if (Storage::disk('public')->exists($filePath)) {
                $file = Storage::disk('public')->get($filePath); // Obtener el archivo
                $type = Storage::disk('public')->mimeType($filePath); // Obtener el tipo MIME del archivo
                // Devolver el archivo como respuesta con los headers correspondientes para forzar la descarga
                return response($file, 200)
                    ->withHeaders([
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                        'Access-Control-Expose-Headers' => 'Content-Disposition',
                        'Access-Control-Allow-Origin' => '*', // O especificar el origen permitido
                        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, Content-Disposition',
                    ]);
            } else {
                // Si el archivo no se encuentra, devolver un error 404
                return response()->json(['error' => 'Archivo no encontrado'], 404);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error con código 500
            return response()->json(['error' => 'Error al intentar descargar el archivo: ' . $e->getMessage()], 500);
        }
    }

    private function cleanFileName($fileName)
    {
        // Elimina "/storage/documentos/" del principio del nombre del archivo
        return Str::replaceFirst('/storage/documentos/', '', $fileName);
    }

    public function getRutaInfo($id)
    {
        try {
            // Obtener la ruta y sus relaciones: actividades, niveles, lecciones, contenido de lecciones, y aliado
            $ruta = Ruta::where('id', $id)->with([
                'actividades' => function ($query) {
                    $query->select('id', 'id_ruta', 'nombre', 'id_aliado');
                },
                'actividades.nivel' => function ($query) {
                    $query->select('id', 'id_actividad', 'nombre', 'id_asesor');
                },
                'actividades.nivel.lecciones' => function ($query) {
                    $query->select('id', 'id_nivel', 'nombre');
                },
                'actividades.nivel.lecciones.contenidoLecciones' => function ($query) {
                    $query->select('id', 'id_leccion', 'titulo');
                },
                'actividades.aliado' => function ($query) {
                    $query->select('id', 'nombre');
                }
            ])->first();

            // Verificar si la ruta fue encontrada, en caso contrario devolver error 404
            if (!$ruta) {
                return response()->json(['error' => 'Ruta no encontrada'], 404);
            }

            // Optimizar la estructura de las actividades y sus niveles, lecciones, y contenido
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

            // Obtener el último nivel, lección, y contenido recorridos
            $ultimoElemento = [
                'nivel_id' => null,
                'leccion_id' => null,
                'contenido_id' => null
            ];

            // Asignar los últimos IDs de nivel, lección y contenido
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

            // Devolver la respuesta con el último elemento encontrado en formato JSON
            return response()->json([
                'ultimo_elemento' => $ultimoElemento
            ]);
        } catch (Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error con código 500
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function idRespuestas($id_emprendedor)
    {
        try {
            // Verificación de roles
            if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
            }
    
            // Buscar todas las empresas asociadas al emprendedor
            $empresas = Empresa::where('id_emprendedor', $id_emprendedor)->get();
    
            if ($empresas->isEmpty()) {
                return response()->json(['error' => 'No se encontraron empresas asociadas a este emprendedor'], 404);
            }
    
            // Verificar si alguna de las empresas tiene al menos una respuesta
            $tieneRespuestas = $empresas->contains(function ($empresa) {
                return $empresa->respuestas()->exists();
            });
    
            return response()->json($tieneRespuestas ? 1 : 0);
    
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}
