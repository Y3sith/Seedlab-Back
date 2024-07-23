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
    public function index(Request $request)
    {
        try {
        if(Auth::user()->id_rol !=1 && Auth::user()->id_rol !=3){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $estado = $request->input('estado', 'Activo'); // Obtener el estado desde el request, por defecto 'Activo'

        $estadoBool = $estado === 'Activo' ? 1 : 0;

        $rutaVer = Ruta::where('estado', $estadoBool)
                ->get(['id', 'nombre', 'fecha_creacion', 'estado', 'imagen_ruta']);

        $rutasi = $rutaVer->map(function ($rutaVers){
            return [
                'id' => $rutaVers->id,
                'nombre' => $rutaVers->nombre,
                'fecha_creacion' => $rutaVers->fecha_creacion,
                'estado' => $rutaVers->estado == 1 ? 'Activo' : 'Inactivo',
                'imagen_ruta' => $rutaVers->imagen_ruta,
            ];
        });
        return response()->json($rutasi);
    } catch (Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    }

        //$ruta = Ruta::with('actividades')->get();
        //$ruta = Actividad::where('id_ruta')->with('id_actividad')->get();
        //$rutas = Ruta::all(); ------
        // foreach ($rutas as $ruta) {
        //     $ruta->imagen_ruta = base64_decode($ruta->imagen_ruta);
        // }
        //return response()->json($rutas); ------
    }

    public function rutaxId($id){
        if(Auth::user()->id_rol !=1 && Auth::user()->id_rol !=3){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }
        $ruta = Ruta::find($id);
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
        // if ($request->hasFile('imagen_ruta')) {
        //     $image = $request->file('imagen_ruta');
        //     $encodedImage = base64_encode(file_get_contents($image->path()));
        // } else {
        //     $encodedImage = null;
        // }

            $ruta = Ruta::create([
            "nombre" => $request->nombre,
            "fecha_creacion"=> Carbon::now(),
            "estado" => 1,
            "imagen_ruta"=>$request->imagen_ruta
            //$encodedImage
        ]);
        return response()->json(["message"=>"Ruta creada exitosamente", $ruta],200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function rutasActivas(){
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
            return response()->json(['Error'=>'No tienes permiso para realizar esta accion'],401);
        }
        $rutasActivas = Ruta::where('estado', 1)->with('actividades.nivel.lecciones.contenidoLecciones')->get();
        return response()->json($rutasActivas);
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
        // $ruta = Ruta::where('id',$id)-> with('actividades.nivel.lecciones.contenidoLecciones')->get();
        
        $ruta = Ruta::with('actividades.nivel.lecciones.contenidoLecciones')->get();

        // if ($ruta) {
        //     $ruta->imagen_ruta = base64_decode($ruta->imagen_ruta);
        // } // Decodificar la imagen


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
                'imagen_ruta'=>$request->imagen_ruta,
                'estado' => $request->estado,
            ]);

             return response()->json(['message'=>'ruta actualizada correctamente',$ruta], 200); //mostrar ruta al actualizar
            //return response()->json(['message'=>'ruta actualizada correctamente'], 200); //mostrar solo el mensaje
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
