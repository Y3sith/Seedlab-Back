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
use App\Models\Aliado;
use Exception;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   


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

    public function userProfileAdmin($id){
        try {
            if (Auth::user()->id_rol!=1) {
                return response()->json(['message'=>'no tienes permiso para esta funcion']);
            }
            $admin = SuperAdmin::where('id',$id)
            ->with('auth:id,email')
            ->select('id','nombre','apellido',"id_autentication")
            ->first();
            return response()->json($admin);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
         }
    }



    /**
     * Update the specified resource in storage.
     */
    public function editarSuperAdmin(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol !=1) {
                return response()->json(['message'=>'no tienes permiso para esta funcion']);
            }
            $admin = SuperAdmin::find($id);
            if ($admin) {
                $admin->nombre=$request->input('nombre');
                $admin->apellido=$request->input('apellido');
                $admin->save();

                if ($admin->auth) {
                    $user=$admin->auth;
                    $password = $request->input('password');
                    if (strlen($password)<8) {
                        $response = 'la contraseña debe ser al menos de 8 caracteres';
                        return response()->json(['message'=>$response]);
                    }
                    $user->email = $request->input('email');
                    $user->password =  Hash::make($request->input('password'));
                    $user->estado=$request->input('estado');
                    $user->save();
                }
                return response()->json(['messaje'=>'Superadministrador actualizado correctamente'],200);
            }else{
                return response()->json(['message'=>'Superadministrador no encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
         }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     if(Auth::user()->id_rol !=1){
    //         return response()->json([
    //            'message' => 'No tienes permiso para acceder a esta ruta'
    //         ], 401);
    //     }

    //     $superAdmin = SuperAdmin::find($id);
    //     if(!$superAdmin){
    //         return response()->json([
    //            'message' => 'SuperAdmin no encontrado'
    //         ], 404);
    //     }

    //     $user = $superAdmin->auth;
    //     $user->estado = 0;
    //     $user->save();

    //     return response()->json(['message' =>'SuperAdmin desactivado'], 200);
       
    // }

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
    
        $averageAsesorias = $this->averageAsesorias2024();
    

        $result['Promedio Anual de AsesoriasxEmprendedor'] = round($averageAsesorias, 2);

        $top = $this->topAliados();

        $result['Top Aliados'] = $top;

    
        return response()->json($result);
    }
    
    public function averageAsesorias2024()
    {
        $averageAsesorias = Asesoria::whereRaw('YEAR(fecha) = 2024') // Cambiar aquí el año desde el front
            ->join(
                DB::raw('(SELECT doc_emprendedor, COUNT(*) as asesoria_count FROM asesoria WHERE YEAR(fecha) = 2024 GROUP BY doc_emprendedor) as asesoria_counts'), // Cambiar aquí el año desde el front
                'asesoria_counts.doc_emprendedor',
                '=',
                'asesoria.doc_emprendedor'
            )->selectRaw('AVG(asesoria_counts.asesoria_count) as average_asesorias')->value('average_asesorias');
    
        return $averageAsesorias;
    }

    public function topAliados(){
       
        $totalAsesorias = Asesoria::count();

            $topAliados = Aliado::withCount('asesoria')
                ->orderByDesc('asesoria_count')
                ->take(5)
                ->get(['nombre', 'asesoria_count']);

            $topAliados->transform(function ($aliado) use ($totalAsesorias) {
                $porcentaje = ($aliado->asesoria_count / $totalAsesorias) * 100;
                $aliado->porcentaje = round($porcentaje, 2) . '%';
                return [
                    'nombre' => $aliado->nombre,
                    'asesorias' => $aliado->asesoria_count,
                    'porcentaje' => $aliado->porcentaje,
                ];
            });    
    return $topAliados;
    }



    
}
