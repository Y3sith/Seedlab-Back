<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Aliado;
use App\Models\Asesoria;
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
        /* if(Auth::user()->id_rol =!1){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }*/
        //muestra los emprendedores por su id
        if (Auth::user()->id_rol = !5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        } $emprendedor = Emprendedor::all();
        return response()->json($emprendedor);
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
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $empresa = Empresa::where('id_emprendedor', $id_emprendedor)->paginate(5);
        if ($empresa->isEmpty()) {
            return response()->json(["error" => "Empresa no encontrada"], 404);
        }
        return response()->json($empresa->items(), 200);
    }


    public function update(){
        
    }














   




    public function destroy($documento)
    {
        if (Auth::user()->id_rol != 5) {
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
