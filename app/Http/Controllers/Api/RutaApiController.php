<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ContenidoLeccion;
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol !=4) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $estado = $request->input('estado', 'Activo'); // Obtener el estado desde el request, por defecto 'Activo'

            $estadoBool = $estado === 'Activo' ? 1 : 0;

            $rutaVer = Ruta::where('estado', $estadoBool)
                ->get(['id', 'nombre', 'fecha_creacion', 'estado']);

            $rutasi = $rutaVer->map(function ($rutaVers) {
                return [
                    'id' => $rutaVers->id,
                    'nombre' => $rutaVers->nombre,
                    'fecha_creacion' => $rutaVers->fecha_creacion,
                    'estado' => $rutaVers->estado == 1 ? 'Activo' : 'Inactivo',
                ];
            });
            return response()->json($rutasi);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }

        //$ruta = Ruta::with('actividades')->get();
        //$ruta = Actividad::where('id_ruta')->with('id_actividad')->get();
        //$rutas = Ruta::all(); ------
        // foreach ($rutas as $ruta) {
        //     $ruta->imagen_ruta = base64_decode($ruta->imagen_ruta);
        // }
        //return response()->json($rutas); ------
    }

    public function rutaxId($id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
            // 'estado' => $ruta->estado == 1 ? 'Activo' : 'Inactivo';
            //$ruta = Ruta::find($id);
            $ruta = Ruta::where('id', $id)
                ->select('id', 'nombre', 'fecha_creacion', 'estado')
                ->first();
            return [
                'id' => $ruta->id,
                'nombre' => $ruta->nombre,
                'fecha_creacion' => $ruta->fecha_creacion,
                'estado' => $ruta->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $nombreRuta = $request->nombre;

            if (strlen($nombreRuta) > 70) {
                return response()->json(['message' => 'El nombre de la ruta no puede tener más de 70 caracteres'], 422);
            }

            $existingRoute = Ruta::where('nombre', $request->nombre)->first();
            if ($existingRoute) {
                return response()->json(['message' => 'El nombre de la ruta ya ha sido registrado anteriormente'], 422);
            }
            $ruta = Ruta::create([
                "nombre" => $nombreRuta,
                "fecha_creacion" => Carbon::now(),
                "estado" => 1,
            ]);
            return response()->json(["message" => "Ruta creada exitosamente", $ruta], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function rutas()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 5) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }
        $rutasActivas = Ruta::where('estado', 1)->get();

        return response()->json($rutasActivas);
    }


    public function rutasActivas()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 5) {
            return response()->json(['Error' => 'No tienes permiso para realizar esta accion'], 401);
        }
        $rutasActivas = Ruta::where('estado', 1)->with('actividades.nivel.lecciones.contenidoLecciones')->get();

        return response()->json($rutasActivas);
    }

    public function mostrarRutaConContenido($id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        // Obtener la ruta por su ID con las actividades y sus niveles, lecciones y contenido por lección
        // $ruta = Ruta::where('id',$id)-> with('actividades.nivel.lecciones.contenidoLecciones')->get();

        $ruta = Ruta::with('actividades.nivel.lecciones.contenidoLecciones')->get();

        // if ($ruta) {
        //     $ruta->imagen_ruta = base64_decode($ruta->imagen_ruta);
        // } // Decodificar la imagen


        // Retornar la ruta con todas las relaciones cargadas
        return response()->json($ruta);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $ruta = Ruta::find($id);

            $newNombre = $request->input('nombre');

            if (strlen($newNombre) > 70) {
                return response()->json(['message' => 'El nombre de la ruta no puede tener más de 70 caracteres'], 422);
            }


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
                //'nombre' => $request->nombre,
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
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = Ruta::find($id);
        if (!$ruta) {
            return response()->json([
                'message' => 'Ruta no encontrada'
            ], 404);
        }
        $ruta->update([
            'estado' => 0,
        ]);
        return response()->json([
            'message' => 'Ruta desactivada exitosamente'
        ], 200);
    }


    public function actnivleccontXruta($id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 5 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
            $ruta = Ruta::where('id', $id)->with('actividades', 'actividades.aliado')->get();
            $ruta = $ruta->map(function ($r) {
                $r->actividades = $r->actividades->map(function ($actividad) {
                    $actividad->id_asesor = $actividad->asesor ? $actividad->asesor->nombre : 'Ninguno';
                    unset($actividad->asesor);
                    //$actividad->id_asesor = $actividad->id_asesor ?? 'Ninguno';
                    $actividad->estado = $actividad->estado == 1 ? 'Activo' : 'Inactivo';
                    $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
                    unset($actividad->aliado);
                    return $actividad;
                });
                return $r;
            });
            return response()->json($ruta);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function actnividadxAliado($id, $id_aliado)
{
    try {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = Ruta::where('id', $id)->with(['actividades' => function($query) use ($id_aliado) {
            if ($id_aliado) {
                $query->where('id_aliado', $id_aliado);
            }
        }, 'actividades.aliado', 'actividades.asesor'])->get();

        $ruta = $ruta->map(function ($r) {
            $r->actividades = $r->actividades->map(function ($actividad) {
                $actividad->id_asesor = $actividad->asesor ? $actividad->asesor->nombre : 'Ninguno';
                unset($actividad->asesor);
                $actividad->estado = $actividad->estado == 1 ? 'Activo' : 'Inactivo';
                $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
                unset($actividad->aliado);
                return $actividad;
            });
            return $r;
        });

        return response()->json($ruta);
    } catch (Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
}

public function actnividadxAsesor($id, $id_asesor)
{
    try {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = Ruta::where('id', $id)->with(['actividades' => function($query) use ($id_asesor) {
            if ($id_asesor) {
                $query->where('id_asesor', $id_asesor);
            }
        }, 'actividades.aliado', 'actividades.asesor'])->get();

        $ruta = $ruta->map(function ($r) {
            $r->actividades = $r->actividades->map(function ($actividad) {
                $actividad->id_asesor = $actividad->asesor ? $actividad->asesor->nombre : 'Ninguno';
                unset($actividad->asesor);
                $actividad->estado = $actividad->estado == 1 ? 'Activo' : 'Inactivo';
                $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
                unset($actividad->aliado);
                return $actividad;
            });
            return $r;
        });

        return response()->json($ruta);
    } catch (Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
}

public function actividadCompletaxruta($id)
{
    try {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 5) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        
        $ruta = Ruta::where('id', $id)
            ->with(['actividades' => function ($query) {
                $query->where('estado', 1);
            }, 'actividades.nivel.lecciones.contenidoLecciones', 'actividades.aliado'])
            ->get();

        $ruta = $ruta->map(function ($r) {
            $r->actividades = $r->actividades->map(function ($actividad) {
                $actividad->id_asesor = $actividad->id_asesor ?? 'Ninguno';
                $actividad->estado = 'Activo';
                $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
                unset($actividad->aliado);
                return $actividad;
            });
            return $r;
        });

        return response()->json($ruta);
    } catch (Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
}

    public function descargarArchivoContenido($contenidoId)
    {
        try {
            $contenidoLeccion = ContenidoLeccion::findOrFail($contenidoId);
            $fileName = $this->cleanFileName($contenidoLeccion->fuente_contenido);
            $filePath = 'documentos/' . $fileName;

            if (Storage::disk('public')->exists($filePath)) {
                $file = Storage::disk('public')->get($filePath);
                $type = Storage::disk('public')->mimeType($filePath);
                return response($file, 200)
                ->withHeaders([
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    'Access-Control-Expose-Headers' => 'Content-Disposition',
                    'Access-Control-Allow-Origin' => '*', // O especifica el origen permitido
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Content-Disposition',
                ]);
            } else {
                return response()->json(['error' => 'Archivo no encontrado'], 404);
            }
        } catch (Exception $e) {
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
            $ruta = Ruta::where('id', $id)->with([
                'actividades' => function ($query) {
                    $query->select('id', 'id_ruta', 'nombre', 'id_asesor', 'id_aliado');
                },
                'actividades.nivel' => function ($query) {
                    $query->select('id', 'id_actividad', 'nombre');
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
    
            if (!$ruta) {
                return response()->json(['error' => 'Ruta no encontrada'], 404);
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
    
            return response()->json([
                'ultimo_elemento' => $ultimoElemento
            ]);
    
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function idRespuestas(){
        try {
            if (Auth::user()->id_rol != 5) {
            return response()->json(['error' => 'Ocurrió un error al procesar']);
        }

        $respuestas = Respuesta::get('id');

        return response()->json( $respuestas);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
        
    }
}
