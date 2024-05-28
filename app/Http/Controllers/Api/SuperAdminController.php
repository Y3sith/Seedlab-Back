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
use App\Models\User;
use App\Models\Rol;
use App\Models\Asesoria;







class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function verEmprendedoresxEmpresa()
    {
        if(Auth::user()->id_rol != 1){
            return response()->json([
               'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();
        
        return response()->json($emprendedoresConEmpresas);
    }


    public function personalizacionSis(Request $request)
    {
        if(Auth::user()->id_rol != 1){
            return response()->json([
               'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }
        
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
    public function crearSuperAdmin(Request $data)
    {
        $response = null;
        $statusCode = 200;

        if(Auth::user()->id_rol != 1){
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        if(strlen($data['password']) <8) {
            $statusCode = 400;
            $response = 'La contraseña debe tener al menos 8 caracteres';
            return response()->json(['message' => $response], $statusCode);
        }

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

    public function enumerarUsuarios() {
        $roles = Rol::all();
        $result = [];

        $totalUsers = User::count();

        foreach ($roles as $rol) {
            $countActive = User::where('id_rol', $rol->id)->where('estado', true)->count();
            $percentageActive = $totalUsers > 0 ? ($countActive / $totalUsers) * 100 : 0;

            $result[$rol->nombre] = [
                '# de usuarios activos' => $countActive,
                'Porcentaje del total' => round($percentageActive, 2) . '%'
            ];
        }

        $activeUsersCount = User::where('estado', true)->count();
        $inactiveUsersCount = User::where('estado', false)->count();

        $activePercentage = $totalUsers > 0 ? ($activeUsersCount / $totalUsers) * 100 : 0;
        $inactivePercentage = $totalUsers > 0 ? ($inactiveUsersCount / $totalUsers) * 100 : 0;

        $result['activos'] = round($activePercentage, 2) . '%';
        $result['inactivos'] = round($inactivePercentage, 2) . '%';

        return response()->json($result);
    }

    public function averageAsesorias2024()
    {
        $averageAsesorias = Asesoria::whereYear('fecha', 2024)//quedad modificar la variable desde front para el filtrado
            ->select(DB::raw('AVG(asesoria_count) as average_asesorias'))
            ->joinSub(
                Asesoria::select('doc_emprendedor', DB::raw('COUNT(*) as asesoria_count'))
                    ->whereYear('fecha', 2024)
                    ->groupBy('doc_emprendedor'),
                'asesoria_counts',
                'asesoria_counts.doc_emprendedor',
                '=',
                'asesoria.doc_emprendedor'
            )
            ->value('average_asesorias');

        return response()->json([
            'average_asesorias' => $averageAsesorias,
        ]);
    }
}
