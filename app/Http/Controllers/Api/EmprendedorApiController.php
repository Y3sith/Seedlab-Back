<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Emprendedor;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\Departamento;
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
        // Verifica si el usuario autenticado no tiene el rol de emprendedor 
        if (Auth::user()->id_rol = !5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        // Obtiene todos los registros de emprendedores de la base de datos
        $emprendedor = Emprendedor::all();

        // Devuelve la lista de emprendedores en formato JSON
        return response()->json($emprendedor);
    }

    public function store(Request $request)
    {
        //crear emprendedor

    }

    /**
     * Muestra las empresas asociadas a un emprendedor específico.
     */
    public function show($id_emprendedor)
    {
        // Verifica si el usuario autenticado tiene el rol de emprendedor.
        if (Auth::user()->id_rol != 5) {
            return response()->json(["message" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        // Obtiene las empresas asociadas al emprendedor.
        $empresa = Empresa::where('id_emprendedor', $id_emprendedor)
            ->select('documento', 'nombre', 'correo', 'direccion', 'id_emprendedor')
            ->paginate();

        // Verifica si no se encontraron empresas.
        if ($empresa->isEmpty()) {
            return response()->json(["message" => "Empresa no encontrada"], 404);
        }

        // Devuelve la lista de empresas en formato JSON.
        return response()->json($empresa->items(), 200);
    }

    public function updateEmprendedor(Request $request, $documento)
    {
        // Verificar si el usuario autenticado tiene el rol adecuado
        if (Auth::user()->id_rol != 5) {
            return response()->json(["message" => "No tienes permisos para editar el perfil"], 401);
        }

        // Obtener el emprendedor actual basado en el documento proporcionado
        $emprendedor = Emprendedor::where('documento', $documento)->first();

        $newCelular = $request->input('celular');
        if ($newCelular && $newCelular !== $emprendedor->celular) {
            // Verificar si el nuevo email ya está en uso
            $existing = Emprendedor::where('celular', $newCelular)->first();
            if ($existing) {
                return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 400);
            }
            $emprendedor->celular = $newCelular;
        }

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
            'id_departamento' => 'required|max:255',
            'id_municipio' => 'required|max:255',
            'id_tipo_documento' => 'required|integer',
            'password' => 'nullable|string|min:8',
            'imagen_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
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
        $emprendedor->id_departamento = $request->id_departamento;
        $emprendedor->id_municipio = $request->id_municipio;

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
                    return response()->json(["message" => "La nueva contraseña no puede ser igual a la contraseña actual"], 400);
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

    public function tipoDocumento()
    {
        $tipoDocumento = TipoDocumento::all();
        return response()->json($tipoDocumento);
    }
}
