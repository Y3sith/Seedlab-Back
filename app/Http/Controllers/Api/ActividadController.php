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
        //crear actividad (solo el aliado)
        if (Auth::user()->id_rol == 3) {
            $actividad = Actividad::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ruta_multi' => $request->ruta_multi,
                'id_tipo_dato' => $request->id_tipo_dato,
                'id_asesor' => $request->id_asesor,
                'id_ruta' => $request->id_ruta,
            ]);
            return response()->json($actividad, 201);
        } else {
            return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
        }
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
        //editar actividad (solo el aliado y asesor)
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 4) {
            $actividad = Actividad::find($id);
            if (!$actividad) {
                return response()->json(["error" => "Actividad no encontrada"], 404);
            } else {
                $actividad->nombre = $request->nombre;
                $actividad->descripcion = $request->descripcion;
                $actividad->ruta_multi = $request->ruta_multi;
                $actividad->id_tipo_dato = $request->id_tipo_dato;
                $actividad->id_asesor = $request->id_asesor;
                $actividad->id_ruta = $request->id_ruta;
                $actividad->update();
                return response(["message" => "Actividad actualizada"], 200);
            }
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
