<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApoyoEmpresa;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Apoyo_por_EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //muestra los apoyos 
        if(Auth::user()->id_rol != 1){
            return response()->json([
               'message' => 'No tiene permisos para acceder a este recurso'
            ], 403);
        }
        $apoyoxempresa = ApoyoEmpresa::paginate(5);
        return new JsonResponse($apoyoxempresa->items());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function crearApoyos(Request $request)
    {
        try{

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyo = ApoyoEmpresa::create([
                'documento' => $request->documento,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'email' => $request->email,
                'id_tipo_documento' => $request->id_tipo_documento,
                'id_empresa' => $request->id_empresa,

            ]);
            return response()->json(['message' => 'Apoyo creado con exito'], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getApoyosxEmpresa($id_empresa)
    {
        try {

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyos = ApoyoEmpresa::all()->where('id_empresa', $id_empresa);
            return response()->json($apoyos, 200);

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
    public function editarApoyo (Request $request, $documento)
    {
        try{

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }
            
            $apoyo = ApoyoEmpresa::where('documento', $documento);
            
            if (!$apoyo) {
            return response()->json(['error' => 'Apoyo no encontrado'], 404);
            }

            $apoyo->update([
                'documento' => $request->input('documento'),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'cargo' => $request->input('cargo'),
                'telefono' => $request->input('telefono'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'id_tipo_documento' => $request->input('id_tipo_documento'),
            ]);

            return response()->json([
                'message' => 'Apoyo editado exitosamente'], 201);

        }catch (Exception $e){
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getApoyoxDocumento($documento)
    {
        try {

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyos = ApoyoEmpresa::all()->where('documento', $documento)->first();
            return response()->json($apoyos, 200);

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
