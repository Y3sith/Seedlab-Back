<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Emprendedor;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmprendedorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //muestra los emprendedores - super administrator
        if(Auth::user()->id_rol =!1){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $emprendedor = Emprendedor::paginate(5);
        return new JsonResponse($emprendedor->items());
    }

    public function store(Request $request)
    {
        //crear emprendedor
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id_emprendedor)
    {
        /* Muestra las empresas asociadas por el emprendedor */
        if(Auth::user()->id_rol !=5){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $empresa = Empresa::where('id_emprendedor', $id_emprendedor)->paginate();
        if ($empresa->isEmpty()) {
            return response()->json(["error" => "Empresa no encontrada"], 404);
        }
        return response()->json($empresa->items(), 200);
    }

    public function update(Request $request, $documento)
    {
        //editar el emprendedor
        if(Auth::user()->id_rol != 5){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $emprendedor = Emprendedor::find($documento);
        if (!$emprendedor) {
            return response([
                'message' => 'Emprendedor no encontrado',
            ], 404);
        }
        $emprendedor->update([
            "nombre" => $request->nombre,
            "apellido" => $request->apellido,
            "celular" => $request->celular,
            "genero" => $request->genero,
            "direccion" => $request->direccion,
            "id_municipio" => $request->id_municipio,
        ]);

        return response()->json(['message' => 'Emprendedor actualizado', $emprendedor, 200]);
    }

    public function destroy($documento)
    {
        if(Auth::user()->id_rol != 5){
            return response()->json(["error" => "No tienes permisos para desactivar la cuenta"], 401);
        }
         //Se busca emprendedor por documento
         $emprendedor = Emprendedor::find($documento);
         //dd($emprendedor);
         if (!$emprendedor) {
             return response()->json([
                 'message' => 'Emprendedor no encontrado'
             ], 404);
         }
 
         // Con la relacion de emprendedor User, en la funcion llamada auth, se trae los datos de la tabla users
         $user = $emprendedor->auth;
         //dd($user);
         $user->estado = 0;
         $user->save();
 
         $emprendedor->email_verified_at = null;
         $emprendedor->save();
 
         return response()->json([
             'message' => 'Emprendedor desactivado exitosamente'
         ], 200);
        }
}
