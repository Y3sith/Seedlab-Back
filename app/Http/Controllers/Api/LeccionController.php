<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leccion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeccionController extends Controller
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
     * Crea una nueva lección en la base de datos.
     * Esta función permite a los asesores crear una lección asociada a un nivel específico.
     * Verifica los permisos del usuario y si ya existe una lección con el mismo nombre para el nivel proporcionado.
     */
    public function store(Request $request)
    {
        // Crear lección (solo el asesor)
        try {
            // Verifica si el usuario autenticado tiene permiso para crear lecciones.
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para crear lecciones'], 401);
            }

            // Comprueba si ya existe una lección con el mismo nombre para el nivel dado.
            $existingNivel = Leccion::where('nombre', $request->nombre)
                ->where('id_nivel', $request->id_nivel)
                ->first();

            if ($existingNivel) {
                return response()->json(['message' => 'Ya existe una lección con este nombre para este nivel'], 422);
            }

            // Crea una nueva lección con los datos proporcionados.
            $leccion = Leccion::create([
                'nombre' => $request->nombre,
                'id_nivel' => $request->id_nivel,
            ]);

            // Devuelve una respuesta indicando que la lección fue creada con éxito.
            return response()->json(['message' => 'Lección creada con éxito ', $leccion], 201);
        } catch (Exception $e) {
            // Captura cualquier excepción y devuelve un mensaje de error.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    public function LeccionxNivel($id)
    {
        //mostrar niveles asociados a una actividad
        try {
            if (Auth::user()->id_rol != 1  && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos '], 401);
            }
            $leccion = Leccion::where('id_nivel', $id)->select('id', 'nombre')->get();
            return response()->json($leccion);
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
     * Actualiza una lección existente en la base de datos.
     */
    public function editarLeccion(Request $request, string $id)
    {
        // Editar solo el asesor
        try {
            // Verifica si el usuario autenticado tiene permiso para editar lecciones.
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol !=3) {
                return response()->json(['error' => 'No tienes permisos para editar lecciones'], 401);
            }

            // Busca la lección por ID.
            $leccion = Leccion::find($id);
            if (!$leccion) {
                return response()->json(['error' => 'Lección no encontrada'], 404);
            } else {
                // Actualiza el nombre de la lección con los datos del request.
                $leccion->nombre = $request->nombre;
                $leccion->update();

                // Devuelve una respuesta indicando que la lección fue actualizada con éxito.
                return response(["message" => "Lección actualizada correctamente"], 201);
            }
        } catch (Exception $e) {
            // Captura cualquier excepción y devuelve un mensaje de error.
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
