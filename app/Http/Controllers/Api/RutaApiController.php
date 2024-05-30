<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Ruta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->id_rol !=1){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        $ruta = Ruta::all();
        return response()->json($ruta);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if(Auth::user()->id_rol != 1){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
            $ruta = Ruta::create([
            "nombre" => $request->nombre,
            "fecha_creacion"  => Carbon::now(),
            "estado" => 1
        ]);
        return response()->json(["message"=>"Ruta creada exitosamente", $ruta],200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */

     
     public function mostrarRutaConContenido($id)
    {
        if(!Auth::user()){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        // Obtener la ruta por su ID con las actividades y sus niveles, lecciones y contenido por lección
        $ruta = Ruta::with('actividades.nivel.lecciones.contenidoLecciones')->get();

        // Retornar la ruta con todas las relaciones cargadas
        return response()->json($ruta);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
             if(Auth::user()->id_rol!=1){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = Ruta::find($id);
        if(!$ruta){
            return response()->json([
               'message' => 'Ruta no encontrada'], 404);
        }
            $ruta->update([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
            ]);

            return response()->json($ruta, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
       
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Auth::user()->id_rol!=1){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $ruta = Ruta::find($id);
        if(!$ruta){
            return response()->json([
               'message' => 'Ruta no encontrada'], 404);
        }
        $ruta->update([
            'estado' => 0,
        ]);
        return response()->json([
            'message' => 'Ruta desactivada exitosamente'
        ], 200);
    }
}
