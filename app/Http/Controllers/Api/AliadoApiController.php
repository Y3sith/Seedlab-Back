<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aliado;

class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $aliados = Aliado::whereHas('auth', function ($query) {
            $query->where('estado', 1);
        })->select('nombre', 'descripcion', 'logo', 'ruta_multi')->get();
        return response()->json($aliados);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        if()
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Auth::user()->id_rol != 3){
            return response()->json([
               'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $aliado = Aliado::find($id);
        if(!$aliado){
            return response()->json([
               'message' => 'Aliado no encontrado'
            ], 404);
        }
        $aliado->update([
            'estado' => 0,
        ]);
        return response()->json([
            'message' => 'Aliado desactivado'
         ], 404);
    }
}
