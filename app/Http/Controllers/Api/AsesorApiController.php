<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Asesor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\HorarioAsesoria;

class AsesorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $data)
    {
        
        $response = null;
        $statusCode = 200;
        if(Auth::user()->id_rol !=3){
            $statusCode = 400;
            $response = 'Solo los aliados pueden crear asesores';
            return response()->json(['message' => $response], $statusCode);
        }
        if(strlen($data['password']) <8) {
            $statusCode = 400;
            $response = 'La contraseña debe tener al menos 8 caracteres';
            return response()->json(['message' => $response], $statusCode);
        }
        
        DB::transaction(function () use ($data, &$response, &$statusCode) {
        $results = DB::select('CALL sp_registrar_asesor(?, ?, ?, ?, ?, ?,?)', [
                $data['nombre'],
                $data['apellido'],
                $data['celular'],
                $data['aliado'],
                $data['email'],
                Hash::make($data['password']),
                $data['estado'],
            ]);


            if (!empty($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El numero de celular ya ha sido registrado en el sistema' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
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
        if(Auth::user()-> id_rol == 3 || Auth::user()-> id_rol ==4){
            $asesor = Asesor::find($id);
            $asesor->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'celular' => $request->celular,
                //'email' => $request->email, no se sabe si pueda editar 
            ]);
            return response()->json(['message' => 'Asesor actualizado', 200]);
        }
        return response()->json([
            'message' => 'No tienes permisos para realizar esta acción'], 403);   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Auth::user()->id_rol != 3){
            return response()->json([
               'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $asesor = Asesor::find($id);
        if (!$asesor) {
            return response()->json([
               'message' => 'Asesor no encontrado',
            ], 404);
        }
        $user = $asesor->auth;
        $user->estado = 0;
        $user->save();
        return response()->json([
           'message' => 'Asesor desactivado',
        ], 200);
    }

    public function mostrarAsesoriasAsesor($id, $conHorario) {
        $asesor = Asesor::find($id);
    
        if (!$asesor) {
            return response()->json([
                'message' => 'El asesor no existe en el sistema'], 404);
        }
    
        $asesoriasAsesor = $asesor->asesorias()->with('emprendedor', 'horarios')->get();
    
        if ($conHorario === 'true') {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isNotEmpty();
            });
        } else {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isEmpty();
            });
        }
    
        $resultado = $asesoriasFiltradas->map(function ($asesoria) {
            $data =[
                'Nombre_sol' => $asesoria->Nombre_sol,
                'notas' => $asesoria->notas,
                'fecha' => $asesoria->fecha,
                'nombre' => $asesoria->emprendedor->nombre,
                'apellido' => $asesoria->emprendedor->apellido,
                'celular' => $asesoria->emprendedor->celular,
                'correo' => $asesoria->emprendedor->auth->email,
            ];
            if($asesoria->horarios->isNotEmpty()){
                $data['observaciones'] = $asesoria->horarios->first()->observaciones;
                $data['fecha_asignacion'] = $asesoria->horarios->first()->fecha;
                $data['estado'] = $asesoria->horarios->first()->estado;
            }
            else{
                $data['mensaje'] = 'No tiene horario asignado';

            }
            return $data;   
        })->values();
    
        return response()->json($resultado, 200);
    }

    public function contarAsesorias($idAsesor) {
        
        $asesor = Asesor::find($idAsesor);

        if (!$asesor) {
            return response()->json([
                'error' => 'Asesor no encontrado'
            ], 404);
        }

        $finalizadas = $asesor->asesorias()->whereHas('horarios', function($query) {
                $query->where('estado', 'Finalizada');
        })->count();

        $pendientes = $asesor->asesorias()->whereHas('horarios', function($query) {
            $query->where('estado', 'Pendiente');
        })->count();

        return response()->json([
            'Asesorias finalizadas' => $finalizadas,
            'Asesorias Pendientes' => $pendientes,
        ]);
    }
    
}
