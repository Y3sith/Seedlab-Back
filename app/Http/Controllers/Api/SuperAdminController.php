<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Emprendedor;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\PersonalizacionSistema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;





class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function ver_emprendedoresxempresa()
    {
        if(Auth::user()->id_rol != 1){
            return response()->json([
               'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();
        
        return response()->json($emprendedoresConEmpresas);
    }


    public function Personalizacion_sis(Request $request)
    {
        $personalizacion = PersonalizacionSistema::create([
            'imagen_Logo' => $request->input('imagen_Logo'),
            'nombre_sistema' => $request->input('nombre_sistema'),
            'color_principal' => $request->input('color_principal'),
            'color_secundario' => $request->input('color_secundario'),
            'id_superadmin' => $request->input('id_superadmin'),
        ]);
    
        return response()->json(['message' => 'Personalización del sistema creada correctamente'], 201);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function crearsuperAdmin(Request $data)
    {
        $response = null;
        $statusCode = 200;

        DB::transaction(function()use ($data, &$response, &$statusCode) {
            $results = DB::select('CALL sp_registrar_superadmin(?,?,?,?,?)', [
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                Hash::make($data['password']),
                $data['estado'],
            ]);

            if (!empty($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    $statusCode = 400;
                }
            }
        });

        return response()->json(['message' => $response], $statusCode);
        
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
    public function update(Request $request, $id)
    {
     //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Auth::user()->id_rol !=1){
            return response()->json([
               'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        $superAdmin = SuperAdmin::find($id);
        if(!$superAdmin){
            return response()->json([
               'message' => 'SuperAdmin no encontrado'
            ], 404);
        }

        $user = $superAdmin->auth;
        $user->estado = 0;
        $user->save();

        return response()->json(['message' =>'SuperAdmin desactivado'], 200);
       
    }
}
