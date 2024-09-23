<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Asesoria;
use App\Models\Banner;
use App\Models\Emprendedor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function traerAliadosActivos($status)
    {
        // 

        $aliados = Aliado::whereHas('auth', fn($query) => $query->where('estado', $status))
            ->with(['tipoDato:id,nombre', 'auth'])
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', 'id_autentication')
            ->get();

        $aliadosTransformados = $aliados->map(function ($aliado) {
            //$banner = Banner::find($aliado->$id_aliado);
            return [
                'id' => $aliado->id,
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $aliado->logo ? $this->correctImageUrl($aliado->logo) : null,
                'ruta_multi' => $aliado->ruta_multi ? $this->correctImageUrl($aliado->ruta_multi) : null,
                'urlpagina' => $aliado->urlpagina,
                'tipo_dato' => $aliado->tipoDato,
                'email' => $aliado->auth->email,
                'estado' => $aliado->auth->estado
            ];
        });

        return response()->json($aliadosTransformados);
    }

    public function traerAliadoxId($id)
    {
        try {

            $aliado = Aliado::where('id', $id)
                ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
                ->first();
            return [
                'id' => $aliado->id,
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $aliado->logo ? $this->correctImageUrl($aliado->logo) : null,
                'ruta_multi' => $aliado->ruta_multi ? $this->correctImageUrl($aliado->ruta_multi) : null,
                'id_tipo_dato' => $aliado->id_tipo_dato,
                'urlpagina' => $aliado->urlpagina,
                'email' => $aliado->auth->email,
                'estado' => $aliado->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function traerAliadosiau($id)
    {
        try {

            $aliado = Aliado::where('id', $id)
                ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
                ->first();
            return [
                'id' => $aliado->id,
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $aliado->logo ? $this->correctImageUrl($aliado->logo) : null,
                'ruta_multi' => $aliado->ruta_multi ? $this->correctImageUrl($aliado->ruta_multi) : null,
                'id_tipo_dato' => $aliado->id_tipo_dato,
                'urlpagina' => $aliado->urlpagina,
                'email' => $aliado->auth->email,
                'estado' => $aliado->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function traerBanners($status)
    {
        // Obtener los banners de la base de datos
        $banners = Banner::where('estadobanner', $status)
            ->select('urlImagen', 'estadobanner')
            ->get();

        $bannersTransformados = $banners->map(function ($banner) {
            return [
                'urlImagen' => $banner->urlImagen ? $this->correctImageUrl($banner->urlImagen) : null,
                'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo'
            ];
        });

        // Devolver los datos
        return response()->json($bannersTransformados, 200);
    }



    public function traerBannersxaliado($id_aliado)
    {

        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $banners = Banner::where('id_aliado', $id_aliado)
                ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
                ->get();

            $bannersTransformados = $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'urlImagen' => $banner->urlImagen ? $this->correctImageUrl($banner->urlImagen) : null,
                    'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo'
                ];
            });
            return response()->json($bannersTransformados);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function traerBannersxID($id)
    {

        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $banners = Banner::where('id', $id)
                ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
                ->first();
            return [
                'id' => $banners->id,
                'urlImagen' => $banners->urlImagen ? $this->correctImageUrl($banners->urlImagen) : null,
                'estadobanner' => $banners->estadobanner == 1 ? 'Activo' : 'Inactivo',
                'id_aliado' => $banners->id_aliado,
            ];
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path);
    }

    public function crearAliado(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;
            $aliadoId = null;
            $youtubeRegex = '/^https:\/\/(www\.)?youtube\.com\/watch\?v=[\w-]{11}$/';
            $rutaMulti = trim($data->input('ruta_multi'));

            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
            }

            if (strlen($data['password']) < 8) {
                return response()->json(['message' => 'La contraseña debe tener al menos 8 caracteres'], 400);
            }



            // Validación del banner
            if (!$data->hasFile('banner_urlImagen') || !$data->file('banner_urlImagen')->isValid()) {
                return response()->json(['message' => 'Se requiere una imagen válida para el banner'], 400);
            }

            if (!$data->hasFile('logo') || !$data->file('logo')->isValid()) {
                return response()->json(['message' => 'Se requiere una imagen válida para el logo'], 400);
            }

            if ($data->input('id_tipo_dato') == 2 || $data->input('id_tipo_dato') == 3) {
                if (!$data->hasFile('ruta_multi') || !$data->file('ruta_multi')->isValid()) {
                    return response()->json(['message' => 'Debe seleccionar un archivo pdf o de imagen válido'], 400);
                }
            } elseif ($data->input('id_tipo_dato') == 1 || $data->input('id_tipo_dato') == 4) {
                if (trim($data->input('ruta_multi')) == null) {
                    return response()->json(['message' => 'El campo de texto no puede estar vacío'], 400);
                }
            }

            $descripcion = $data->input('descripcion');
            if (strlen($descripcion) < 206) {
                return response()->json(['message' => 'La descripción debe tener al menos 206 caracteres'], 400);
            }
            if (strlen($descripcion) > 314) {
                return response()->json(['message' => 'La descripción no puede tener más de 312 caracteres'], 400);
            }

            DB::beginTransaction();

            try {
                $logoUrl = null;
                if ($data->hasFile('logo') && $data->file('logo')->isValid()) {
                    $logoPath = $data->file('logo')->store('public/logos');
                    $logoUrl = Storage::url($logoPath);
                }

                $rutaMulti = null;
                if ($data->hasFile('ruta_multi')) {
                    $file = $data->file('ruta_multi');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();

                    if (strpos($mimeType, 'image') !== false) {
                        $folder = 'imagenes';
                    } elseif ($mimeType === 'application/pdf') {
                        $folder = 'documentos';
                    } elseif ($mimeType === 'application/pdf') {
                        $folder = 'documentos';
                    } else {
                        return response()->json(['message' => 'Tipo de archivo no soportado para ruta_multi'], 400);
                    }

                    $path = $file->storeAs("public/$folder", $fileName);
                    $rutaMulti = Storage::url($path);
                } elseif ($data->input('ruta_multi') && filter_var($data->input('ruta_multi'), FILTER_VALIDATE_URL)) {
                    $rutaMulti = $data->input('ruta_multi');
                } elseif ($data->input('ruta_multi')) {
                    // Si se envió un texto en 'ruta_multi', se guarda como texto
                    $rutaMulti = $data->input('ruta_multi');
                }

                $results = DB::select('CALL sp_registrar_aliado(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $logoUrl,
                    $data['descripcion'],
                    $data['id_tipo_dato'],
                    $rutaMulti,
                    $data['urlpagina'],
                    $data['email'],
                    Hash::make($data['password']),
                    $data['estado'] === 'true' ? 1 : 0,
                ]);

                if (empty($results)) {
                    throw new \Exception('No se recibió respuesta del procedimiento almacenado');
                }

                $response = $results[0]->mensaje;
                $aliadoId = $results[0]->id;

                if ($response === 'El nombre del aliado ya se encuentra registrado' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    throw new \Exception($response);
                }

                // Procesar el banner
                $bannerPath = $data->file('banner_urlImagen')->store('public/banners');
                $bannerUrl = Storage::url($bannerPath);

                Banner::create([
                    'urlImagen' => $bannerUrl,
                    'estadobanner' => $data['banner_estadobanner'],
                    'id_aliado' => $aliadoId,
                ]);

                DB::commit();
                Log::info('Aliado y banner creados:', ['aliadoId' => $aliadoId, 'response' => $response]);

                return response()->json(['message' => 'Aliado creado exitosamente'], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error al crear aliado y banner:', ['message' => $e->getMessage()]);
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function crearBanner(Request $request)
    {
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
        }

        if (!$request->hasFile('urlImagen') || !$request->file('urlImagen')->isValid()) {
            return response()->json(['message' => 'Se requiere una imagen válida para el banner'], 400);
        }

        $bannerCount = Banner::where('id_aliado', $request->id_aliado)->count();

        if ($bannerCount >= 3) {
            return response()->json(['message' => 'Ya existen 3 banners para este aliado. Debe eliminar un banner antes de crear uno nuevo.'], 400);
        }

        if ($request->hasFile('urlImagen') && $request->file('urlImagen')->isValid()) {
            $bannerPath = $request->file('urlImagen')->store('public/banners');
            $bannerUrl = Storage::url($bannerPath);
        }

        $banner = Banner::create([
            'urlImagen' => $bannerUrl,
            'estadobanner' => $request->estadobanner,
            'id_aliado' => $request->id_aliado,
        ]);
        Log::info('Datos del banner antes de guardar:', $banner->toArray());

        return response()->json([
            'message' => 'Banner creado exitosamente',
        ], 201);
    }

    public function editarBanner(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            $banner = Banner::find($id);

            // Actualizar la imagen si se envió una nueva
            if ($request->hasFile('urlImagen')) {
                // Eliminar la imagen anterior
                Storage::delete(str_replace('storage', 'public', $banner->urlImagen));

                // Guardar la nueva imagen
                $paths = $request->file('urlImagen')->store('public/banners');
                $banner->urlImagen = str_replace('public', 'storage', $paths);
            }

            // Actualizar el estado del banner
            $banner->estadobanner = $request->input('estadobanner');
            $banner->save();

            return response()->json([
                'message' => 'Banner editado exitosamente',
                'banner' => $banner
            ], 201);
        } catch (Exception $e) {
            Log::error('Error en editarBanner: ' . $e->getMessage());
            Log::error('Datos de la solicitud: ' . json_encode($request->all()));
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarBanner($id)
    {

        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner no encontrado'], 404);
        }

        $url = str_replace('storage', 'public', $banner->urlImagen);

        Storage::delete($url);
        $banner->delete();

        return response()->json(['message' => 'Banner eliminado correctamente'], 200);
    }


    public function editarAliado(Request $request, $id)
    {
        try {
            // Buscar al aliado por ID
            $aliado = Aliado::find($id);
            if (!$aliado) {
                return response()->json(['error' => 'Aliado no encontrado'], 404);
            }

            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            $descripcion = $request->input('descripcion');
            if (strlen($descripcion) < 206) {
                return response()->json(['error' => 'La descripción debe tener al menos 206 caracteres'], 400);
            }
            if (strlen($descripcion) > 312) {
                return response()->json(['error' => 'La descripción no puede tener más de 312 caracteres'], 400);
            }

            $user = $aliado->auth;
            Log::info('Usuario antes de guardar:', $user->toArray());
            // Validar nombre igual
            $newNombre = $request->input('nombre');
            if ($newNombre && $newNombre !== $aliado->nombre) {
                $existing = Aliado::where('nombre', $newNombre)->first();
                if ($existing) {
                    return response()->json(['message' => 'El nombre del Aliado ya ha sido registrado anteriormente'], 400);
                }
                $aliado->nombre = $newNombre;
            }


            if ($request->hasFile('ruta_multi')) {
                // Si se está subiendo un nuevo archivo
                $file = $request->file('ruta_multi');
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
                if ($aliado->ruta_multi && Storage::exists(str_replace('storage', 'public', $aliado->ruta_multi))) {
                    Storage::delete(str_replace('storage', 'public', $aliado->ruta_multi));
                }

                // Guardar el nuevo archivo
                $path = $file->storeAs("public/$folder", $fileName);
                $aliado->ruta_multi = str_replace('public', 'storage', $path);
            } elseif ($request->filled('ruta_multi')) {
                $newRutaMulti = $request->input('ruta_multi');

                // Si es una URL (asumiendo que es de YouTube)
                if (filter_var($newRutaMulti, FILTER_VALIDATE_URL)) {
                    // Tu código existente para manejar URLs
                    if ($aliado->ruta_multi && Storage::exists(str_replace('storage', 'public', $aliado->ruta_multi))) {
                        Storage::delete(str_replace('storage', 'public', $aliado->ruta_multi));
                    }
                    $aliado->ruta_multi = $newRutaMulti;
                } else {
                    // Si es texto, simplemente guardarlo
                    // Eliminar el archivo anterior si existe
                    if ($aliado->ruta_multi && Storage::exists(str_replace('storage', 'public', $aliado->ruta_multi))) {
                        Storage::delete(str_replace('storage', 'public', $aliado->ruta_multi));
                    }
                    $aliado->ruta_multi = $newRutaMulti;
                }
            }

            if ($request->hasFile('logo')) {
                //Eliminar el logo anterior
                Storage::delete(str_replace('storage', 'public', $aliado->logo));

                // Guardar el nuevo logo
                $path = $request->file('logo')->store('public/logos');
                $aliado->logo = str_replace('public', 'storage', $path);
            }


            // Actualizar los datos del aliado
            Log::info('Datos recibidos para actualización:', $request->all());
            $aliado->update([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'id_tipo_dato' => $request->input('id_tipo_dato'),
                'ruta_multi' => $aliado->ruta_multi,
                'urlpagina' => $request->input('urlpagina'),
            ]);

            // Actualizar la contraseña del usuario si se proporciona una nueva
            $password = $request->input('password');
            if ($password) {
                $user->password = Hash::make($password);
            }

            // Actualizar el email del usuario si es diferente
            $newEmail = $request->input('email');
            if ($newEmail && $newEmail !== $user->email) {
                $existingUser = User::where('email', $newEmail)->first();
                if ($existingUser) {
                    return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
                }
                $user->email = $newEmail;
            }

            // Actualizar el estado del usuario
            $user->estado = filter_var($request->input('estado'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            Log::info('Usuario antes de guardar:', $user->toArray());
            $user->save();

            Log::info('Aliado antes de guardar:', $aliado->toArray());


            return response()->json(['message' => 'Aliado actualizado', $user], 200);
        } catch (Exception $e) {
            Log::error('Error en editarAliado: ' . $e->getMessage());
            Log::error('Datos de la solicitud: ' . json_encode($request->all()));
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }






    public function mostrarAliado(Request $request)
    {
        $aliado = Aliado::with(['auth', 'tipoDato'])->find($request->input('id'));

        if ($aliado) {
            $logoBase64 = $aliado->logo ? 'data:image/png;base64,' . $aliado->logo : null;

            $estado = $aliado->auth ? $aliado->auth->estado : null;

            $tipoDato = $aliado->tipoDato ? $aliado->tipoDato->nombre : null;

            return response()->json([
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $logoBase64,
                'ruta_multi' => $aliado->ruta_multi,
                'id_autentication' => $aliado->id_autentication,
                'id_tipo_dato' => $tipoDato,
                'estado' => $estado == 1 ? "Activo" : "Inactivo",
                'message' => 'Aliado creado exitosamente',
                200
            ]);
        } else {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    public function update(Request $request, string $id)
    {
        //

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 1) {

            $aliado = Aliado::find($id);
            if (!$aliado) {
                return response()->json([
                    'message' => 'Aliado no encontrado',
                ], 404);
            }
            $user = $aliado->auth;
            $user->estado = 0;
            $user->save();

            return response()->json([
                'message' => 'Aliado desactivado',
            ], 200);
        }

        return response()->json([
            'message' => 'No tienes permisos para realizar esta acción'
        ], 403);
    }


    public function mostrarAsesorAliado(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;
            $aliado = Aliado::find($id);
            if (!$aliado) {
                return response()->json(['message' => 'No se encontró ningún aliado con este ID'], 404);
            }
            $asesores = Aliado::findOrFail($id)->asesor()
                ->whereHas('auth', function ($query) use ($estadoBool) {
                    $query->where('estado', $estadoBool);
                })
                ->select(
                    'id',
                    'id_aliado',
                    'nombre',
                    'apellido',
                    'imagen_perfil',
                    'documento',
                    'id_tipo_documento',
                    'fecha_nac',
                    'direccion',
                    'genero',
                    'id_municipio',
                    'celular',
                    'id_autentication'
                )
                ->get();
            $asesoresConEstado = $asesores->map(function ($asesor) {
                $user = User::find($asesor->id_autentication);
                return [
                    'id' => $asesor->id,
                    'nombre' => $asesor->nombre,
                    'apellido' => $asesor->apellido,
                    'imagen_perfil' => $asesor->imagen_perfil ? $this->correctImageUrl($asesor->imagen_perfil) : null,
                    'documento' => $asesor->documento,
                    'id_tipo_documento' => $asesor->id_tipo_documento,
                    'fecha_nac' => $asesor->fecha_nac,
                    'direccion' => $asesor->direccion,
                    'genero' => $asesor->genero,
                    'celular' => $asesor->celular,
                    'id_municipio' => $asesor->id_municipio,
                    'id_aliado' => $asesor->id_aliado,
                    'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo',
                    'email' => $user->email
                ];
            });
            return response()->json($asesoresConEstado);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    public function gestionarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
            }

            $asesoriaId = $request->input('id_asesoria');
            $accion = $request->input('accion'); // aceptar o rechazar

            $asesoria = Asesoria::find($asesoriaId);

            if (!$asesoria || $asesoria->id_aliado != Auth::user()->aliado->id) {
                return response()->json(['message' => 'Asesoría no encontrada o no asignada a este aliado'], 404);
            } elseif ($accion === 'rechazar') {
                //$horario->estado = 'rechazada';
                $asesoria->id_aliado = null;  // Establecer id_aliado a null
                $asesoria->isorientador = true;
                $asesoria->save(); // Guardar cambios en la asesoria
                $mensaje = 'Asesoría rechazada correctamente';
            } else {
                return response()->json(['message' => 'Acción no válida'], 400);
            }

            //$horario->save();

            return response()->json(['message' => $mensaje], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function verEmprendedoresxEmpresa()
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();

        return response()->json($emprendedoresConEmpresas);
    }

    public function asesoriasXmes($id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta funciona.']);
            }
            $ano = date('Y');
            $asesorias = Asesoria::where('id_aliado', $id)
                ->whereYear('fecha', $ano)
                ->selectRaw('MONTH(fecha) as mes, COUNT(*) as total') //selecciona el mes y luego cuenta las asesorias
                ->groupBy('mes')
                ->get();

            return response()->json($asesorias);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
