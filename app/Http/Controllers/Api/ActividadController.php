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
use App\Mail\NotificacionActividadAliado;
use Illuminate\Support\Facades\Mail;

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
    public function store(Request $request) // Función para almacenar una actividad
    {
        try {
            // Verifica que el usuario tenga permisos adecuados
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(["error" => "No tienes permisos para crear una actividad"], 401);
            }
            $destinatario = null;

            // Busca el aliado correspondiente
            $aliado = Aliado::find($request['id_aliado']);
            if (!$aliado) {
                return response()->json(['message' => 'Aliado no encontrado'], 404);
            }
            $destinatario = $aliado;

            // Valida los datos recibidos
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                'id_ruta' => 'required|integer|exists:ruta,id',
                'id_aliado' => 'required|integer|exists:aliado,id'
            ]);

            // Verifica si la actividad ya existe
            $existingActividad = Actividad::where([
                ['nombre', $validatedData['nombre']],
                ['descripcion', $validatedData['descripcion']],
                ['id_tipo_dato', $validatedData['id_tipo_dato']],
                ['id_ruta', $validatedData['id_ruta']],
                ['id_aliado', $validatedData['id_aliado']]
            ])->first();

            if ($existingActividad) {
                return response()->json(['error' => 'La actividad ya existe'], 409);
            }

            // Maneja la carga de la fuente (archivo o URL)
            $fuente = null;

            if ($request->hasFile('fuente')) {
                $file = $request->file('fuente');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
            
                // Determina el tipo de carpeta según el tipo de archivo
                if (strpos($mimeType, 'image') !== false) {
                    $folder = 'imagenes';
                    $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.webp';
                    $path = "public/$folder/$fileName";
                    
                    // Convertir la imagen a WebP
                    $sourceImage = $this->createImageFromFile($file->path());
                    if ($sourceImage) {
                        $fullPath = storage_path("app/$path");
                        imagewebp($sourceImage, $fullPath, 80);
                        imagedestroy($sourceImage);
                        $fuente = Storage::url($path);
                    } else {
                        return response()->json(['message' => 'Formato de imagen invalido, intente con otro formato'], 400);
                    }
                } elseif ($mimeType === 'application/pdf') {
                    $folder = 'documentos';
                    $path = $file->storeAs("public/$folder", $fileName);
                    $fuente = Storage::url($path);
                } else {
                    return response()->json(['message' => 'Tipo de archivo no soportado para fuente.'], 400);
                }
            } elseif ($request->input('fuente')) {
                $fuenteInput = $request->input('fuente');
                if (filter_var($fuenteInput, FILTER_VALIDATE_URL)) {
                    // Es una URL válida
                    $fuente = $fuenteInput;
                } else {
                    // Es texto plano
                    $fileName = time() . '_fuente.txt';
                    $path = "public/textos/$fileName";
                    Storage::put($path, $fuenteInput);
                    $fuente = Storage::url($path);
                }
            }
            
            // Verifica si $fuente tiene un valor antes de crear la actividad
            if ($fuente === null) {
                return response()->json(['message' => 'No se proporcionó una fuente válida.'], 400);
            }

            // Crea la actividad en la base de datos
            $actividad = Actividad::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'fuente' => $fuente,
                'id_tipo_dato' => $validatedData['id_tipo_dato'],
                'id_ruta' => $validatedData['id_ruta'],
                'id_aliado' => $validatedData['id_aliado'],
                'estado' => 1
            ]);

            // Envía notificación por email al aliado
            $destinatario->load('auth');
            if (Auth::user()->id_rol != 3){
                if ($destinatario->auth && $destinatario->auth->email) {
                    $nombreActividad = $actividad->nombre;
                    Mail::to($destinatario->auth->email)->send(new NotificacionActividadAliado($nombreActividad, $destinatario->nombre));
                }
            }

            // Devuelve respuesta de éxito
            return response()->json(['message' => 'Actividad creada con éxito ', $actividad, $destinatario], 201);
        } catch (Exception $e) {
            // Manejo de excepciones
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    private function createImageFromFile($filePath)
{
    $imageInfo = getimagesize($filePath);
    if ($imageInfo === false) {
        return false;
    }

    $mimeType = $imageInfo['mime'];

    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            return imagecreatefromjpeg($filePath);
        case 'image/png':
            return imagecreatefrompng($filePath);
        case 'image/gif':
            return imagecreatefromgif($filePath);
        case 'image/bmp':
        case 'image/x-ms-bmp':
            return imagecreatefrombmp($filePath);
        case 'image/webp':
            return imagecreatefromwebp($filePath);
        default:
            return false;
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
                'id_aliado' => 'required|integer|exists:aliado,id',
                'estado' => 'required'
            ]);

            $destinatario = null;

            // Busca el aliado correspondiente
            $aliado = Aliado::find($request['id_aliado']);
            if (!$aliado) {
                return response()->json(['message' => 'Aliado no encontrado'], 404);
            }
            $destinatario = $aliado;

            // Obtener la actividad a editar
            $actividad = Actividad::find($id);
            if (!$actividad) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }
            // Actualizar fuente si se ha proporcionado un archivo o una URL
            if ($request->hasFile('fuente')) {
                // Eliminar la imagen anterior
                Storage::delete(str_replace('storage', 'public', $actividad->fuente));
            
                $file = $request->file('fuente');
                $fileName = uniqid('actividad_') . '.webp';
                $path = 'public/imagenes/' . $fileName;
            
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'webp') {
                    // Si ya es WebP, simplemente mover el archivo
                    $file->storeAs('public/imagenes', $fileName);
                } else {
                    // Crear una imagen desde el archivo original
                    $sourceImage = $this->createImageFromFile($file->path());
            
                    if ($sourceImage) {
                        $fullPath = storage_path('app/' . $path);
                        // Guardar la imagen como WebP
                        imagewebp($sourceImage, $fullPath, 80);
                        // Liberar memoria
                        imagedestroy($sourceImage);
                    } else {
                        return response()->json(['message' => 'Formato de imagen invalido, intente con otro formato'], 400);
                    }
                }
                $actividad->fuente = str_replace('public', 'storage', $path);
            }

            $id_aliado_anterior = $actividad->id_aliado;


            // Actualizar la actividad
            $actividad->update([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'id_tipo_dato' => $validatedData['id_tipo_dato'],
                'id_aliado' => $validatedData['id_aliado'],
                'estado' => $validatedData['estado']
            ]);

            if ($id_aliado_anterior != $validatedData['id_aliado'] && Auth::user()->id_rol != 3) {
                $nuevoAliado = Aliado::find($validatedData['id_aliado']);
                if ($nuevoAliado && $nuevoAliado->auth && $nuevoAliado->auth->email) {
                    $nombreActividad = $actividad->nombre;
                    Mail::to($nuevoAliado->auth->email)->send(new NotificacionActividadAliado($nombreActividad, $nuevoAliado->nombre));
                }
            }

            return response()->json(['message' => 'Actividad actualizada con éxito', 'actividad' => $actividad], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function Activar_Desactivar_Actividad($id)
    {
        try {
            // Verifica que el usuario tenga permisos para activar/desactivar la actividad
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json('no tienes permiso para desactivar la actividad', 400);
            }

            // Busca la actividad por su ID
            $actividad = Actividad::find($id);
            if (!$actividad) {
                return response()->json('Actividad no encontradas', 400);
            }

            // Cambia el estado de la actividad
            $nuevoEstado = !$actividad->estado;
            $actividad->update(['estado' => $nuevoEstado]);

            // Responde con un mensaje de éxito
            return response()->json(['message' => 'Estado de la Actividad actualizado correctamente']);
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
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
        // Verifica que el usuario tenga permisos para acceder a esta ruta
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }

        // Obtiene todos los tipos de dato (id y nombre) de la base de datos
        $dato = TipoDato::get(['id', 'nombre']);

        // Responde con los tipos de dato en formato JSON
        return response()->json($dato);
    }

    public function VerActividadAliado($id)
    {
        // Verifica que el usuario tenga el rol adecuado para acceder a esta ruta
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }

        // Obtiene las actividades asociadas al aliado con el ID proporcionado
        $actividades = Actividad::where('id_aliado', $id)
            ->select('id', 'nombre', 'descripcion', 'fuente', 'id_tipo_dato', 'id_asesor', 'id_ruta',)
            ->get();

        // Responde con las actividades en formato JSON
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
            if (!$actividad) {
                return response()->json(['message' => 'Actividad no encontrada'], 404);
            }
            return response()->json($actividad);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function actividadAsesor($id)
    { //traer actividad,nivel,leccion y contenido por leccion a base de la actividad
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json([
                    'messaje' => 'No tienes permisos para acceder a esta ruta'
                ], 401);
            }
            $actividad = Actividad::where('id', $id)
                ->first();

            if (!$actividad) {
                return response()->json(['message' => 'Actividad no encontrada'], 404);
            }
            return response()->json($actividad);
            
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
