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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //crear leccion (solo el asesor)
        try {
            if (Auth::user()->id_rol != 1  && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para crear lecciones'], 401);
            }
            $leccion = Leccion::create([
                'nombre' => $request->nombre,
                'id_nivel' => $request->id_nivel,
            ]);
            return response()->json(['message' => 'Lección creada con éxito: ', $leccion], 201);
        } catch (Exception $e) {
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


    public function LeccionxNivel($id){
        //mostrar niveles asociados a una actividad
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message'=> 'No tienes permisos '],401);
            }
            $leccion = Leccion::where('id_nivel', $id)->select('id','nombre')->get();
            return response()->json($leccion);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: '. $e->getMessage()], 500);
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
    public function editarLeccion(Request $request, string $id)
    {
        //editar solo el asesor
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para editar lecciones'], 401);
            }
            $leccion = Leccion::find($id);
            if (!$leccion) {
                return response()->json(['error' => 'Leccion no encontrada'], 404);
            } else {
                $leccion->nombre = $request->nombre;
                $leccion->update();
                return response(["message" => "Leccion actualizada correctamente"], 201);
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
