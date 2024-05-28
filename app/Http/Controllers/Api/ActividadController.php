<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ver todas las actividades (asesor/aliado/emprendedor por hacer)
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 4 | Auth::user()->id_rol == 5) {
            $actividad = Actividad::all();
            return response()->json($actividad);
        }
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
        // Crear actividad (solo el aliado)
        if (Auth::user()->id_rol !== 3) {
            return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'ruta_multi' => 'required|string|max:255',
            'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
            'id_asesor' => 'required|integer|exists:asesor,id',
            'id_ruta' => 'required|integer|exists:ruta,id',
        ]);

        // Verificar si la actividad ya existe
        $existingActividad = Actividad::where([
            ['nombre', $validatedData['nombre']],
            ['descripcion', $validatedData['descripcion']],
            ['ruta_multi', $validatedData['ruta_multi']],
            ['id_tipo_dato', $validatedData['id_tipo_dato']],
            ['id_asesor', $validatedData['id_asesor']],
            ['id_ruta', $validatedData['id_ruta']]
        ])->first();

        if ($existingActividad) {
            return response()->json(['error' => 'La actividad ya existe'], 409);
        }

        $actividad = Actividad::create([
            'nombre' => $validatedData['nombre'],
            'descripcion' => $validatedData['descripcion'],
            'ruta_multi' => $validatedData['ruta_multi'],
            'id_tipo_dato' => $validatedData['id_tipo_dato'],
            'id_asesor' => $validatedData['id_asesor'],
            'id_ruta' => $validatedData['id_ruta'],
        ]);

        return response()->json(['message' => 'Actividad creada con éxito'], 201);
    }

    /** 
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //muestra actividad especifica
        $actividad = Actividad::find($id);
        if (!$actividad) {
            return response()->json(["error" => "Actividad no encontrada"], 404);
        } else {
            return response()->json($actividad, 200);
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
    public function update(Request $request, string $id)
{
    // Solo pueden editar la actividad los usuarios con roles 3 (aliado) o 4 (asesor)
    if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 4) {
        $actividad = Actividad::find($id);
        if (!$actividad) {
            return response()->json(["error" => "Actividad no encontrada"], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'ruta_multi' => 'required|string|max:255',
            'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
            'id_asesor' => 'required|integer|exists:asesor,id',
        ]);

        // Verificar si los valores nuevos son diferentes de los existentes
        $cambios = false;
        if ($actividad->nombre !== $validatedData['nombre']) {
            $actividad->nombre = $validatedData['nombre'];
            $cambios = true;
        }
        if ($actividad->descripcion !== $validatedData['descripcion']) {
            $actividad->descripcion = $validatedData['descripcion'];
            $cambios = true;
        }
        if ($actividad->ruta_multi !== $validatedData['ruta_multi']) {
            $actividad->ruta_multi = $validatedData['ruta_multi'];
            $cambios = true;
        }
        if ($actividad->id_tipo_dato !== $validatedData['id_tipo_dato']) {
            $actividad->id_tipo_dato = $validatedData['id_tipo_dato'];
            $cambios = true;
        }
        if ($actividad->id_asesor !== $validatedData['id_asesor']) {
            $actividad->id_asesor = $validatedData['id_asesor'];
            $cambios = true;
        }

        if (!$cambios) {
            return response()->json(["message" => "No se realizaron cambios, los datos son iguales"], 400);
        }
        $actividad->save();

        return response()->json(["message" => "Actividad actualizada con éxito"], 200);
    } else {
        return response()->json(["error" => "No tienes permisos para editar esta actividad"], 403);
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
