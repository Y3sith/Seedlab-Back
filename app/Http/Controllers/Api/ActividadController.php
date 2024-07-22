<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Aliado;
use App\Models\TipoDato;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ver todas las actividades (asesor/aliado/emprendedor por hacer)
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 4 | Auth::user()->id_rol == 5) {
            $actividad = Actividad::all();
            return response()->json($actividad);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Crear actividad (solo el aliado)
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
            return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'ruta_multi' => 'required|string|max:255',
            'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
            'id_asesor' => 'required|integer|exists:asesor,id',
            'id_ruta' => 'required|integer|exists:ruta,id',
            'id_aliado'=> 'required|integer|exists:aliado,id'
        ]);

        // Verificar si la actividad ya existe
        $existingActividad = Actividad::where([
            ['nombre', $validatedData['nombre']],
            ['descripcion', $validatedData['descripcion']],
            ['ruta_multi', $validatedData['ruta_multi']],
            ['id_tipo_dato', $validatedData['id_tipo_dato']],
            ['id_asesor', $validatedData['id_asesor']],
            ['id_ruta', $validatedData['id_ruta']],
            ['id_aliado', $validatedData['id_aliado']]
        ])->first();

        if ($existingActividad) {
            return response()->json(['error' => 'La actividad ya existe'], 409);
        }

        $actividad = Actividad::create([
            'nombre' => $validatedData['nombre'],
            'descripcion' => $validatedData['descripcion'],
            'ruta_multi' => $validatedData['ruta_multi'],
            'id_tipo_dato' => $validatedData['id_tipo_dato'],
            'id_asesor' => $validatedData['id_asesor'],
            'id_ruta' => $validatedData['id_ruta'],
            'id_aliado'=> $validatedData['id_aliado']
        ]);

        return response()->json(['message' => 'Actividad creada con éxito'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
        
    }

    /** 
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //muestra actividad especifica
        $actividad = Actividad::find($id);
        if (!$actividad) {
            return response()->json(["error" => "Actividad no encontrada"], 404);
        } else {
            return response()->json($actividad, 200);
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
    public function update(Request $request, string $id)
{
    // Solo pueden editar la actividad los usuarios con roles 3 (aliado) o 4 (asesor)
    try {
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 4) {
        $actividad = Actividad::find($id);
        if (!$actividad) {
            return response()->json(["error" => "Actividad no encontrada"], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'ruta_multi' => 'required|string|max:255',
            'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
            'id_asesor' => 'required|integer|exists:asesor,id',
        ]);

        // Verificar si los valores nuevos son diferentes de los existentes
        $cambios = false;
        if ($actividad->nombre !== $validatedData['nombre']) {
            $actividad->nombre = $validatedData['nombre'];
            $cambios = true;
        }
        if ($actividad->descripcion !== $validatedData['descripcion']) {
            $actividad->descripcion = $validatedData['descripcion'];
            $cambios = true;
        }
        if ($actividad->ruta_multi !== $validatedData['ruta_multi']) {
            $actividad->ruta_multi = $validatedData['ruta_multi'];
            $cambios = true;
        }
        if ($actividad->id_tipo_dato !== $validatedData['id_tipo_dato']) {
            $actividad->id_tipo_dato = $validatedData['id_tipo_dato'];
            $cambios = true;
        }
        if ($actividad->id_asesor !== $validatedData['id_asesor']) {
            $actividad->id_asesor = $validatedData['id_asesor'];
            $cambios = true;
        }

        if (!$cambios) {
            return response()->json(["message" => "No se realizaron cambios, los datos son iguales"], 400);
        }
        $actividad->save();

        return response()->json(["message" => "Actividad actualizada con éxito"], 200);
    } else {
        return response()->json(["error" => "No tienes permisos para editar esta actividad"], 403);
    }
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

    public function tipoDato(){
        if (Auth::user()->id_rol !=1 &&Auth::user()->id_rol !=3 && Auth::user()->id_rol !=4) { //esto hay que cambiarlo para que solo lo puedan ver algunos roles
            return response()->json([
                'messaje'=>'No tienes permisos para acceder a esta ruta'
            ],401);
        }
        $dato= TipoDato::get(['id','nombre']);
        return response()->json($dato);
    }

    public function VerActividadAliado($id){
        if (Auth::user()->id_rol!=3 && Auth::user()->id_rol !=4) {
            return response()->json([
                'messaje'=>'No tienes permisos para acceder a esta ruta'
            ],401);
        }
        $actividades = Actividad::where('id_aliado', $id)
                    ->select('id', 'nombre', 'descripcion','ruta_multi','id_tipo_dato','id_asesor','id_ruta',)
                    ->get();
            return response()->json($actividades);
    }
}
