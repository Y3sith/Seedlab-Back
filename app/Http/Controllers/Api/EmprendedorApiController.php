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
use Illuminate\Support\Facades\Hash;

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


    public function update(Request $request, $documento)
{
    // Verificar si el usuario autenticado tiene el rol adecuado
    if (Auth::user()->id_rol != 5) {
        return response()->json(["error" => "No tienes permisos para editar el perfil"], 401);
    }

    // Obtener el emprendedor actual basado en el documento proporcionado
    $emprendedor = Emprendedor::where('documento', $documento)->first();

    // Validar si se encontró el emprendedor
    if (!$emprendedor) {
        return response()->json(["error" => "El emprendedor no fue encontrado"], 404);
    }

    // Actualizar los datos del emprendedor con los valores proporcionados en la solicitud
    $emprendedor->nombre = $request->nombre;
    $emprendedor->apellido = $request->apellido;
    $emprendedor->celular = $request->celular;
    $emprendedor->genero = $request->genero;
    $emprendedor->fecha_nac = $request->fecha_nac;
    $emprendedor->direccion = $request->direccion;
    $emprendedor->id_municipio = $request->id_municipio;
    $emprendedor->id_tipo_documento = $request->id_tipo_documento;

// Verificar si se proporcionó una contraseña para actualizar
if ($request->has('password')) {
    if (strlen($request->password) < 8) {
        return response()->json(["error" => "La contraseña debe tener al menos 8 caracteres"], 400);
    }
    $emprendedor->load('auth');
    // Verificar si existe un usuario asociado al emprendedor
    if ($emprendedor->auth) {
        $user = $emprendedor->auth;
        // Verificar si la nueva contraseña es diferente de la contraseña actual
        if (Hash::check($request->password, $user->password)) {
            return response()->json(["error" => "La nueva contraseña no puede ser igual a la contraseña actual"], 400);
        }
        // Actualizar la contraseña en el modelo User asociado al Emprendedor
        $user->password = Hash::make($request->password);
        $user->save();
    } else {
        return response()->json(["error" => "No se encontró un usuario asociado al emprendedor"], 404);
    }
}
    $emprendedor->save();
    return response()->json(['message' => 'Datos del emprendedor actualizados correctamente'], 200);
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
