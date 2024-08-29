<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Aliado;
use App\Models\TipoDato;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
            }

            // if ($request->input('id_tipo_dato') == 2 || $request->input('id_tipo_dato') == 3) {
            //     if (!$request->hasFile('fuente') || !$request->file('fuente')->isValid()) {
            //         return response()->json(['message' => 'Debe seleccionar un archivo pdf o de imagen válido'], 400);
            //     }
            // } 
    
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string|max:1000',
                // 'fuente' => 'required',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                'id_asesor' => 'required|integer|exists:asesor,id',
                'id_ruta' => 'required|integer|exists:ruta,id',
                'id_aliado'=> 'required|integer|exists:aliado,id'
            ]);
    
            // Verificar si la actividad ya existe
            $existingActividad = Actividad::where([
                ['nombre', $validatedData['nombre']],
                ['descripcion', $validatedData['descripcion']],
                // ['fuente', $validatedData['fuente']],
                ['id_tipo_dato', $validatedData['id_tipo_dato']],
                ['id_asesor', $validatedData['id_asesor']],
                ['id_ruta', $validatedData['id_ruta']],
                ['id_aliado', $validatedData['id_aliado']]
            ])->first();
    
            if ($existingActividad) {
                return response()->json(['error' => 'La actividad ya existe'], 409);
            }

            $fuente = null;
                if ($request->hasFile('fuente')) {
                    $file = $request->file('fuente');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();

                    if (strpos($mimeType, 'image') !== false) {
                        $folder = 'imagenes';
                    } elseif ($mimeType === 'application/pdf') {
                        $folder = 'documentos';
                    } elseif ($mimeType === 'application/pdf') {
                        $folder = 'documentos';
                    } else {
                        return response()->json(['message' => 'Tipo de archivo no soportado para fuente'], 400);
                    }

                    $path = $file->storeAs("public/$folder", $fileName);
                    $fuente = Storage::url($path);
                } elseif ($request->input('fuente') && filter_var($request->input('fuente'), FILTER_VALIDATE_URL)) {
                    $fuente = $request->input('fuente');
                } elseif ($request->input('fuente')) {
                    // Si se envió un texto en 'fuente', se guarda como texto
                    $fuente = $request->input('fuente');
                }
    
            $actividad = Actividad::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                //'fuente' => $validatedData['fuente'],
                'fuente' => $fuente, 
                'id_tipo_dato' => $validatedData['id_tipo_dato'],
                'id_asesor' => $validatedData['id_asesor'],
                'id_ruta' => $validatedData['id_ruta'],
                'id_aliado'=> $validatedData['id_aliado']
            ]);
            return response()->json(['message' => 'Actividad creada con éxito: ',$actividad], 201);
            
        }catch (Exception $e) {
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
    public function editarActividad(Request $request, string $id)
{
    // Solo pueden editar la actividad los usuarios con roles 3 (aliado) o 4 (asesor)
    try {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json(["error" => "No tienes permisos para editar esta actividad"], 403);
        }
        $actividad = Actividad::find($id);
        if (!$actividad) {
            return response()->json(["error" => "Actividad no encontrada"], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'fuente' => 'required',
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
        if ($actividad->fuente !== $validatedData['fuente']) {
            $actividad->fuente = $validatedData['fuente'];
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
                    ->select('id', 'nombre', 'descripcion','fuente','id_tipo_dato','id_asesor','id_ruta',)
                    ->get();
            return response()->json($actividades);
    }
}
