<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Emprendedor;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class EmprendedorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (Auth::user()->id_rol = !5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }$emprendedor = Emprendedor::all();
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
        $empresa = Empresa::where('id_emprendedor', $id_emprendedor)->paginate();
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

        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'celular' => 'required|string|max:15',
            'genero' => 'required|string|',
            'fecha_nac' => 'required|date',
            'direccion' => 'required|string|max:255',
            'id_municipio' => 'required|string|max:255', // Validar el nombre del municipio
            'id_tipo_documento' => 'required|integer',
            'password' => 'nullable|string|min:8',
            'imagen_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        
        $municipio = Municipio::where('nombre', $request->id_municipio)->first();
        if (!$municipio) {
            return response()->json(["error" => "El municipio no fue encontrado"], 404);
        }
        if ($request->hasFile('imagen_perfil')) {
            //Eliminar el logo anterior
            Storage::delete(str_replace('storage', 'public', $emprendedor->imagen_perfil));
            
            // Guardar el nuevo logo
            $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
            $emprendedor->imagen_perfil = str_replace('public', 'storage', $path);
        }
        
        // Actualizar los datos del emprendedor con los valores proporcionados en la solicitud
        $emprendedor->nombre = $request->nombre;
        $emprendedor->apellido = $request->apellido;
        $emprendedor->celular = $request->celular;
        $emprendedor->genero = $request->genero;
        $emprendedor->fecha_nac = $request->fecha_nac;
        $emprendedor->direccion = $request->direccion;
        $emprendedor->id_municipio = $municipio->id;
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
                'message' => 'Emprendedor no encontrado',
            ], 404);
        }

        // Con la relacion de emprendedor User, en la funcion llamada auth, se trae los datos de la tabla users
        $user = $emprendedor->auth;
        //dd($user);
        $user->estado = 0;
        $user->save();
        
        
        $emprendedor->email_verified_at = null;
        $emprendedor->save();
            
        return response()->json(['message' => 'Emprendedor desactivado exitosamente. Por favor, inicie sesión de nuevo.'], 200);
    }

    public function tipoDocumento(){
        $tipoDocumento = TipoDocumento::all();
        return response()->json($tipoDocumento);
    }
}
