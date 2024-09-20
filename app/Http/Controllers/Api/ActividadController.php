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
    public function store(Request $request)///toca cambiarlo para dejar solo imagen
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
            }
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                // 'id_asesor' => 'nullable|integer|exists:asesor,id',
                'id_ruta' => 'required|integer|exists:ruta,id',
                'id_aliado' => 'required|integer|exists:aliado,id'
            ]);
            $descripcion = $request->input('descripcion');
            if (strlen($descripcion) < 300) {
                return response()->json(['message' => 'La descripción debe tener al menos 300 caracteres'], 400);
            }
            if (strlen($descripcion) > 470) {
                return response()->json(['message' => 'La descripción no puede tener más de 470 caracteres'], 400);
            }

            // Verificar si la actividad ya existe
            $existingActividad = Actividad::where([
                ['nombre', $validatedData['nombre']],
                ['descripcion', $validatedData['descripcion']],
                ['id_tipo_dato', $validatedData['id_tipo_dato']],
                // ['id_asesor', $validatedData['id_asesor']],
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
                'descripcion' => $descripcion,
                'fuente' => $fuente,
                'id_tipo_dato' => $validatedData['id_tipo_dato'],
                // 'id_asesor' => $validatedData['id_asesor'] ?? null,
                'id_ruta' => $validatedData['id_ruta'],
                'id_aliado' => $validatedData['id_aliado'],
                'estado' => 1
            ]);
            return response()->json(['message' => 'Actividad creada con éxito ', $actividad], 201);

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
    public function editarActividad(Request $request, $id)
    {
        try {
            // Verificar permisos del usuario
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(["error" => "No tienes permisos para editar esta actividad"], 401);
            }

            // Validar datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                //'fuentes' => 'nullable',
                //'id_asesor' => 'nullable|integer|exists:asesor,id',
                'id_aliado' => 'required|integer|exists:aliado,id',
                'estado' => 'required'
            ]);

            // Obtener la actividad a editar
            $actividad = Actividad::find($id);
            if (!$actividad) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }
            // Actualizar fuente si se ha proporcionado un archivo o una URL
            if ($request->hasFile(('fuente'))) {
                Storage::delete(str_replace('storage', 'public', $actividad->fuente));

                $paths = $request->file('fuente')->store('public/imagenes');
                $actividad->fuente = str_replace('public', 'storage', $paths);
            }
            

            // Actualizar la actividad
            $actividad->update([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                //'fuente' => $validatedData['fuente'],
                'id_tipo_dato' => $validatedData['id_tipo_dato'],
                // 'id_asesor' => $validatedData['id_asesor'] ?? null,
                'id_aliado' => $validatedData['id_aliado'],
                'estado' => $validatedData['estado']
            ]);

            return response()->json(['message' => 'Actividad actualizada con éxito', 'actividad' => $actividad], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function Activar_Desactivar_Actividad($id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json('no tienes permiso para desactivar la actividad', 400);
            }

            $actividad = Actividad::find($id);
            if (!$actividad) {
                return response()->json('Actividad no encontradas', 400);
            }
            $nuevoEstado = !$actividad->estado;
            $actividad->update(['estado' => $nuevoEstado]);
            return response()->json(['message' => 'Estado de la Actividad actualizado correctamente']);
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

    public function tipoDato()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) { //esto hay que cambiarlo para que solo lo puedan ver algunos roles
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }
        $dato = TipoDato::get(['id', 'nombre']);
        return response()->json($dato);
    }

    public function VerActividadAliado($id)
    {
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }
        $actividades = Actividad::where('id_aliado', $id)
            ->select('id', 'nombre', 'descripcion', 'fuente', 'id_tipo_dato', 'id_asesor', 'id_ruta', )
            ->get();
        return response()->json($actividades);
    }

    public function ActiNivelLeccionContenido($id)
    { //traer actividad,nivel,leccion y contenido por leccion a base de la actividad
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json([
                    'messaje' => 'No tienes permisos para acceder a esta ruta'
                ], 401);
            }
            $actividad = Actividad::with('nivel.lecciones.contenidoLecciones') //toca cambiar para que traiga el nombre del tipo de dato lo mismo en el contenido
                ->where('id', $id)
                ->first();
            // $actividad->id_asesor = $actividad->asesor ? $actividad->asesor->nombre : 'Ninguno';
            // unset($actividad->asesor);
            // $actividad->id_aliado = $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado';
            // unset($actividad->aliado);

            if (!$actividad) {
                return response()->json(['message' => 'Actividad no encontrada'], 404);
            }
            return response()->json($actividad);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
