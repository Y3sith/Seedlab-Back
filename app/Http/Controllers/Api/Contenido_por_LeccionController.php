<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContenidoLeccion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Contenido_por_LeccionController extends Controller
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
        //crea contenido a la leccion solo el asesor
        try {
            if (Auth::user()->id_rol==4) {
                $contenidoxleccion = ContenidoLeccion::create([
                    'titulo'=>$request->titulo,
                    'descripcion'=>$request->descripcion,
                    'fuente'=>$request->fuente,
                    'id_tipo_dato'=>$request->id_tipo_dato,
                    'id_leccion'=>$request->id_leccion,
                ]);
                return response()->json($contenidoxleccion,201);
            }else{
                return response()->json(["message"=>"No tienes permisos para crear contenido"],401);
            }
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
        try {
            if (Auth::user()->id_rol==4) {
            $contenidoxleccion = ContenidoLeccion::find($id);
            if (!$contenidoxleccion) {
                return response()->json(['error'=>'contenido no encontrado'],404);
            }else {
                $contenidoxleccion->titulo=$request->titulo;
                $contenidoxleccion->descripcion=$request->descripcion;
                $contenidoxleccion->fuente=$request->fuente;
                $contenidoxleccion->id_tipo_dato=$request->id_tipo_dato;
                $contenidoxleccion->update();
                return response(["message"=>"Contenido actualizado correctamente"],201);
            }
        }else {
            return response()->json(["message"=>"No tienes permisos para editar contenido"],401);
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
