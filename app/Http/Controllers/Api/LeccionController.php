<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leccion;
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
        if (Auth::user()->id_rol==4) {
            $leccion = Leccion::create([
                'nombre' => $request->nombre,
                'id_nivel' => $request->id_nivel,
            ]);
            return response()->json($leccion,201);
        }else {
            return response()->json(['error' => 'No tienes permisos para crear niveles'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //editar solo el asesor
        if (Auth::user()->id_rol==4) {
            $leccion = Leccion::find($id);
            if (!$leccion) {
                return response()->json(['error' => 'Leccion no encontrada'], 404);
            } else {
                $leccion->nombre = $request->nombre;
                $leccion->update();
                return response(["message"=>"Leccion actualizada correctamente"],201);
            }
        }else {
            return response()->json(['error' => 'No tienes permisos para editar lecciones'], 401);
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
