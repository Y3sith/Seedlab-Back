<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Models\Actividad;
use App\Models\Asesor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionesActividadAsesor;

class NivelesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Crea un nuevo nivel en la base de datos.
     */
    public function store(Request $request)
{
    try {
        // Verifica si el usuario autenticado tiene permiso para crear niveles.
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(['error' => 'No tienes permisos para crear niveles'], 401);
        }

        // Comprueba si ya existe un nivel con el mismo nombre para la actividad dada.
        $existingNivel = Nivel::where('nombre', $request->nombre)
            ->where('id_actividad', $request->id_actividad)
            ->first();

        if ($existingNivel) {
            return response()->json(['message' => 'Ya existe un nivel con este nombre para esta actividad'], 422);
        }

        // Inicializa las variables para asesor y actividad.
        $asesor = null;
        $actividad = null;

        // Busca el asesor si se proporciona un id_asesor.
        if ($request->id_asesor) {
            $asesor = Asesor::find($request->id_asesor);
        }

        // Busca la actividad si se proporciona un id_actividad.
        if ($request->id_actividad) {
            $actividad = Actividad::find($request->id_actividad);
        }

        // Si la actividad no se encuentra, devuelve un error.
        if (!$actividad) {
            return response()->json(['message' => 'No se pudo crear el nivel debido a que la actividad no fue encontrada'], 422);
        }

        // Crea un nuevo nivel con los datos proporcionados.
        $niveles = Nivel::create([
            'nombre' => $request->nombre,
            'id_asesor' => $request->id_asesor,
            'id_actividad' => $request->id_actividad,
        ]);

        // Si hay un asesor asociado y tiene un correo electrónico, envía una notificación.
        if ($asesor && $asesor->auth && $asesor->auth->email) {
            $nombreActividad = $actividad->nombre;
            $nombreniveles = $niveles->nombre;
            Mail::to($asesor->auth->email)->send(new NotificacionesActividadAsesor($nombreActividad, $nombreniveles, $asesor->nombre));
        }

        // Devuelve una respuesta indicando que el nivel fue creado con éxito.
        return response()->json(['message' => 'Nivel creado con éxito', 'nivel' => $niveles], 201);
    } catch (Exception $e) {
        // Captura cualquier excepción y devuelve un mensaje de error.
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function listarNiveles()
    {
        //proximamente mostrar niveles asociados a actividades o viseversa
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tiened permisos '], 401);
            }
            $nivel = Nivel::all()->select('id', 'nombre');
            return response()->json($nivel);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function NivelxActividad($id)
    {
        //mostrar niveles asociados a una actividad
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos '], 401);
            }
            $nivel = Nivel::where('id_actividad', $id)->select('id', 'nombre', 'id_asesor')->get();
            return response()->json($nivel);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }




    public function NivelxActividadxAsesor($id_actividad, $id_asesor)
    {
        //mostrar niveles asociados a una actividad por el id del asesor
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos '], 401);
            }
            // Filtrar los niveles por id_actividad y id_asesor
            $niveles = Nivel::where('id_actividad', $id_actividad)
                ->where('id_asesor', $id_asesor)
                ->with('asesor:id,nombre') // Incluimos el nombre del asesor en la respuesta
                ->get();

            // Verificar si se encontraron niveles
            if ($niveles->isEmpty()) {
                return response()->json(['error' => 'No se encontraron niveles para esta actividad y asesor'], 404);
            }

            // Formatear la respuesta
            $respuesta = $niveles->map(function ($nivel) {
                return [
                    'id' => $nivel->id,
                    'nombre' => $nivel->nombre,
                    'id_asesor' => $nivel->id_asesor,
                    'asesor' => $nivel->asesor->nombre ?? 'Sin asesor',
                ];
            });

            // Retornar la respuesta en formato JSON
            return response()->json($respuesta);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function editarNivel(Request $request, string $id)
    {
        //Edita solo el asesor
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(["error" => "no estas autorizado para editar"], 401);
            }

            $asesor = null;
            $actividad = null;

        // Busca el asesor si se proporciona un id_asesor.
        if ($request->id_asesor) {
            $asesor = Asesor::find($request->id_asesor);
        }

        // Busca la actividad si se proporciona un id_actividad.
        if ($request->id_actividad) {
            $actividad = Actividad::find($request->id_actividad);
        }

        // Si la actividad no se encuentra, devuelve un error.
        if (!$actividad) {
            return response()->json(['message' => 'No se pudo crear el nivel debido a que la actividad no fue encontrada'], 422);
        }
        
            $niveles = Nivel::find($id);

            $id_asesor_anterior = $niveles->id_asesor;

            if (!$niveles) {
                return response()->json(["error" => "Nivel no encontrado"], 404);
            } else {
                $niveles->nombre = $request->nombre;
                $niveles->id_asesor = $request->id_asesor;
                $niveles->update();

                if ($id_asesor_anterior != $request->id_asesor) {
                    $nuevoAsesor = Asesor::find($request->id_asesor);
                    if ($nuevoAsesor && $nuevoAsesor->auth && $nuevoAsesor->auth->email && $actividad) {
                        $nombreActividad = $actividad->nombre;
                        $nombreNivel = $niveles->nombre;
                        Mail::to($nuevoAsesor->auth->email)->send(new NotificacionesActividadAsesor($nombreActividad, $nombreNivel, $nuevoAsesor->nombre));
                    }
                }

                return response(["messsaje" => "Nivel actualizado correctamente"], 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
