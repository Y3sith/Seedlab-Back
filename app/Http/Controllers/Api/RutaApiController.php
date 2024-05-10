<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruta = Ruta::all();
        return response()->json($ruta);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*user=Auth::user();
        if($user->rol_id != 3){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }*/
        //COLOCAR CAMPO ESTADO EN LA TABLA RUTA, PARA ACTIVAR Y DESACTIVAR RUTAS
        $ruta = new Ruta();
        $ruta->nombre = $request->nombre;
        $ruta->fecha_creacion  = Carbon::now();
        $ruta->save();
        return response()->json($ruta);
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
        $ruta = Ruta::find($id);
        if(!$ruta){
            return response()->json([
               'message' => 'Ruta no encontrada'], 404);
        }
        else{
            $ruta->nombre = $request->nombre;
            $ruta->save();
            return response()->json($ruta, 200);
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
