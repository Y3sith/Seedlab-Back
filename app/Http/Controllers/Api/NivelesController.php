<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Exception;
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
        try {
            if (Auth::user()->id_rol != 1 &&  Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para crear niveles'], 401);
            }
            $niveles = Nivel::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'id_actividad' => $request->id_actividad,
            ]);
            return response()->json($niveles, 201);
        } catch (Exception $e) {
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
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message'=> 'No tiened permisos '],401);
            }
        $nivel = Nivel::all()->select('id','nombre');
        return response()->json($nivel);
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
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4) {
                return response()->json(["error" => "no estas autorizado para editar"], 401);
            }
            $niveles = Nivel::find($id);
            if (!$niveles) {
                return response()->json(["error" => "Nivel no encontrado"], 404);
            } else {
                $niveles->nombre = $request->nombre;
                $niveles->descripcion = $request->descripcion;
                $niveles->update();
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
