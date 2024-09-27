<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContenidoLeccion;
use App\Models\TipoDato;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Contenido_por_LeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // Intenta crear contenido para la lección, acceso restringido a ciertos roles
        try {
            // Verifica si el usuario autenticado tiene un rol permitido (1, 3 o 4)
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                // Si el rol no es permitido, devuelve un mensaje de error con estado 401
                return response()->json(["message" => "No tienes permisos para crear contenido"], 401);
            }

            // Busca contenido existente con el mismo título en la misma lección
            $existingContenido = ContenidoLeccion::where('titulo', $request->titulo)
                ->where('id_leccion', $request->id_leccion)
                ->first();

            // Si ya existe contenido con ese título, devuelve un mensaje de conflicto con estado 409
            if ($existingContenido) {
                return response()->json(['message' => 'El título para esta lección ya existe'], 409);
            }

            // Inicializa la variable fuente
            $fuente = null;

            // Verifica si se ha subido un archivo en 'fuente_contenido'
            if ($request->hasFile('fuente_contenido')) {
                // Obtiene el archivo subido
                $file = $request->file('fuente_contenido');
                // Crea un nombre único para el archivo
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Obtiene el tipo MIME del archivo
                $mimeType = $file->getMimeType();

                // Determina el folder de almacenamiento según el tipo de archivo
                if (strpos($mimeType, 'image') !== false) {
                    $folder = 'imagenes'; // Si es una imagen, usa el folder 'imagenes'
                } elseif ($mimeType === 'application/pdf') {
                    $folder = 'documentos'; // Si es un PDF, usa el folder 'documentos'
                } else {
                    // Si el tipo de archivo no es soportado, devuelve un mensaje de error con estado 400
                    return response()->json(['message' => 'Tipo de archivo no soportado para fuente'], 400);
                }

                // Almacena el archivo y guarda la URL
                $path = $file->storeAs("public/$folder", $fileName);
                $fuente = Storage::url($path);
            } elseif ($request->input('fuente_contenido') && filter_var($request->input('fuente_contenido'), FILTER_VALIDATE_URL)) {
                // Si se envió una URL válida, la asigna a fuente
                $fuente = $request->input('fuente_contenido');
            } elseif ($request->input('fuente_contenido')) {
                // Si se envió texto en 'fuente_contenido', se guarda como texto
                $fuente = $request->input('fuente_contenido');
            }

            // Crea una nueva entrada de contenido de lección en la base de datos
            $contenidoxleccion = ContenidoLeccion::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fuente_contenido' => $fuente,
                'id_tipo_dato' => $request->id_tipo_dato,
                'id_leccion' => $request->id_leccion,
            ]);

            // Devuelve una respuesta JSON indicando que la creación fue exitosa con estado 201
            return response()->json(['message' => 'Contenido de Lección creada con éxito ', $contenidoxleccion], 201);
        } catch (Exception $e) {
            // Si ocurre un error durante el proceso, devuelve un mensaje de error con estado 500
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function editarContenidoLeccion(Request $request, string $id)
    {
        //editar solo el asesor
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(["message" => "No tienes permisos para editar contenido"], 401);
            }
            $contenidoxleccion = ContenidoLeccion::find($id);
            if (!$contenidoxleccion) {
                return response()->json(['error' => 'contenido no encontrado'], 404);
            }

            if ($request->hasFile('fuente_contenido')) {
                // Si se está subiendo un nuevo archivo
                $file = $request->file('fuente_contenido');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Determinar el tipo de archivo
                $mimeType = $file->getMimeType();

                if (strpos($mimeType, 'image') !== false) {
                    $folder = 'imagenes';
                } elseif ($mimeType === 'application/pdf') {
                    $folder = 'documentos';
                } else {
                    return response()->json(['error' => 'Tipo de archivo no soportado'], 400);
                }

                // Eliminar el archivo anterior si existe
                if ($contenidoxleccion->fuente_contenido && Storage::exists(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido))) {
                    Storage::delete(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido));
                }

                // Guardar el nuevo archivo
                $path = $file->storeAs("public/$folder", $fileName);
                $contenidoxleccion->fuente_contenido = str_replace('public', 'storage', $path);
            } elseif ($request->filled('fuente_contenido')) {
                $newFuenteContenido = $request->input('fuente_contenido');

                // Si es una URL (asumiendo que es de YouTube)
                if (filter_var($newFuenteContenido, FILTER_VALIDATE_URL)) {
                    // Tu código existente para manejar URLs
                    if ($contenidoxleccion->fuente_contenido && Storage::exists(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido))) {
                        Storage::delete(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido));
                    }
                    $contenidoxleccion->fuente_contenido = $newFuenteContenido;
                } else {
                    // Si es texto, simplemente guardarlo
                    // Eliminar el archivo anterior si existe
                    if ($contenidoxleccion->fuente_contenido && Storage::exists(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido))) {
                        Storage::delete(str_replace('storage', 'public', $contenidoxleccion->fuente_contenido));
                    }
                    $contenidoxleccion->fuente_contenido = $newFuenteContenido;
                }
            }

            $contenidoxleccion->update([
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'id_leccion' => $request->input('id_leccion'),
                'id_tipo_dato' => $request->input('id_tipo_dato')
            ]);

            return response()->json(["message" => "Contenido actualizado correctamente", "data" => $contenidoxleccion], 200);
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

    public function tipoDatoContenido()
    {
        // Verifica si el usuario autenticado tiene un rol permitido (1, 3 o 4)
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            // Si el rol no es permitido, devuelve un mensaje de error con estado 401
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }

        // Obtiene todos los tipos de dato, solo los campos 'id' y 'nombre'
        $dato = TipoDato::get(['id', 'nombre']);

        // Devuelve la lista de tipos de dato en formato JSON
        return response()->json($dato);
    }

    public function verContenidoPorLeccion($id)
    {
        // Verifica si el usuario autenticado tiene un rol permitido (1, 3 o 4)
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            // Si el rol no es permitido, devuelve un mensaje de error con estado 401
            return response()->json([
                'messaje' => 'No tienes permisos para acceder a esta ruta'
            ], 401);
        }

        // Obtiene todos los contenidos asociados a la lección especificada por su ID
        $datos = ContenidoLeccion::where('id_leccion', $id)->get();

        // Devuelve la lista de contenidos en formato JSON
        return response()->json($datos);
    }
}
