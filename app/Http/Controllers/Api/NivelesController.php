<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //crear nivel solo asesor
        if (Auth::user()->id_rol==4) {
            $niveles = Nivel::create([
                'nombre'=>$request->nombre,
                'descripcion'=>$request->descripcion,
                'id_actividad'=>$request->id_actividad,
            ]);
            return response()->json($niveles,201);
        }else {
            return response()->json(['error' => 'No tienes permisos para crear niveles'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //proximamente mostrar niveles asociados a actividades o viseversa
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
        //crear solo el asesor
        if (Auth::user()->id_rol==4) {
            $niveles = Nivel::find($id);
            if (!$niveles) {
                return response()->json(["error"=>"Nivel no encontrado"],404);
            }else {
                $niveles->nombre=$request->nombre;
                $niveles->descripcion=$request->descripcion;
                $niveles->update();
                return response(["messsaje"=>"Nivel actualizado correctamente"],200);
            }
        }else {
            return response()->json(["error"=>"no estas autorizado para editar"],401);
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
