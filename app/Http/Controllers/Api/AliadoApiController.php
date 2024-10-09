<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionCrearUsuario;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // public function traerAliadosActivos($status)
    // {
    //     // Obtiene los aliados que tienen un estado activo según el parámetro recibido

    //     $aliados = Aliado::whereHas('auth', fn($query) => $query->where('estado', $status))
    //         // Carga las relaciones necesarias para optimizar las consultas
    //         ->with(['tipoDato:id,nombre', 'auth'])
    //         // Selecciona los campos relevantes de la tabla Aliado
    //         ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', 'id_autentication')
    //         ->get();

    //     // Transforma la colección de aliados para adaptarla a la respuesta deseada
    //     $aliadosTransformados = $aliados->map(function ($aliado) {
    //         return [
    //             'id' => $aliado->id,
    //             'nombre' => $aliado->nombre,
    //             'descripcion' => $aliado->descripcion,
    //             'logo' => $aliado->logo ? $this->correctImageUrl($aliado->logo) : null,
    //             'ruta_multi' => $aliado->ruta_multi ? $this->correctImageUrl($aliado->ruta_multi) : null,
    //             'urlpagina' => $aliado->urlpagina,
    //             'tipo_dato' => $aliado->tipoDato,
    //             'email' => $aliado->auth->email,
    //             'estado' => $aliado->auth->estado
    //         ];
    //     });

    //     // Devuelve la colección transformada como respuesta en formato JSON
    //     return response()->json($aliadosTransformados);
    // }
    public function traerAliadosActivos($status)
    {
        $aliados = Aliado::whereHas('auth', function ($query) use ($status) {
            $query->where('estado', $status);
        })
            ->with([
                'tipoDato:id,nombre',
                'auth:id,email,estado'
            ])
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', 'id_autentication')
            ->get();

        // Devuelve los aliados directamente sin transformaciones
        return response()->json($aliados);
    }


    public function traerAliadoxId($id)
    {
        try {
            // Busca un aliado por su ID
            $aliado = Aliado::where('id', $id)
                // Selecciona los campos relevantes de la tabla Aliado
                ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
                ->first();

            // Devuelve los detalles del aliado en un formato específico
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
            // Maneja cualquier excepción y devuelve un mensaje de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function mostrarAliados(Request $request)
    {
        try {
            // Verifica si el usuario tiene el rol adecuado para realizar la acción
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 401);
            }

            // Obtiene el estado desde la solicitud, por defecto 'Activo'
            $estado = $request->input('estado', 'Activo');

            // Convierte el estado a un valor booleano
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            // Obtiene los IDs de los usuarios con el estado especificado y rol 3 (aliado)
            $aliadoVer = User::where('estado', $estadoBool)
                ->where('id_rol', 3)
                ->pluck('id');

            // Busca los aliados que tienen IDs de autenticación en $aliadoVer
            $aliados = Aliado::whereIn('id_autentication', $aliadoVer)
                ->with('auth:id,email,estado')
                ->get(['id', 'nombre', 'id_autentication']);

            // Mapea los aliados para incluir el estado y el email del usuario autenticado
            $aliadosConEstado = $aliados->map(function ($aliado) {
                $user = User::find($aliado->id_autentication);

                return [
                    'id' => $aliado->id,
                    'nombre' => $aliado->nombre,
                    'id_auth' => $user->id,
                    'email' => $user->email,
                    'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo'
                ];
            });

            // Devuelve los aliados en formato JSON
            return response()->json($aliadosConEstado, 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            // Maneja cualquier excepción y devuelve un mensaje de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /*
    Esta funcion es para la vista de todos los aliados sin autorizacion del middleware, donde solo retorno las imagenes , la url y la ruta multi
    */

    public function traerAliadosiau($id)
    {
        try {
            // Busca un aliado por su ID y selecciona los campos necesarios
            $aliado = Aliado::where('id', $id)
                ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
                ->first();

            // Prepara la respuesta con los datos del aliado
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
            // Maneja cualquier excepción y devuelve un mensaje de error
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
            // Verifica si el usuario tiene los permisos necesarios
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene los banners asociados al aliado
            $banners = Banner::where('id_aliado', $id_aliado)
                ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
                ->get();

            // Transforma la colección de banners para añadir URLs corregidas y estados en texto
            $bannersTransformados = $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    // Corrige la URL de la imagen si existe
                    'urlImagen' => $banner->urlImagen ? $this->correctImageUrl($banner->urlImagen) : null,
                    // Convierte el estado del banner a texto
                    'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo'
                ];
            });
            // Devuelve los banners transformados en formato JSON
            return response()->json($bannersTransformados);
        } catch (Exception $e) {
            // Maneja cualquier excepción y devuelve un mensaje de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function traerBannersxID($id)
    {
        try {
            // Verifica si el usuario tiene los permisos necesarios
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Busca un banner específico por su ID
            $banners = Banner::where('id', $id)
                ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
                ->first();

            // Devuelve los datos del banner encontrado
            return [
                'id' => $banners->id,
                'urlImagen' => $banners->urlImagen ? $this->correctImageUrl($banners->urlImagen) : null,
                'estadobanner' => $banners->estadobanner == 1 ? 'Activo' : 'Inactivo',
                'id_aliado' => $banners->id_aliado,
            ];
        } catch (Exception $e) {
            // Maneja cualquier excepción y devuelve un mensaje de error
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

            DB::beginTransaction();

            try {

                $generateRandomPassword = function ($length = 8) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $password = '';
                    for ($i = 0; $i < $length; $i++) {
                        $password .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    return $password;
                };


                $logoUrl = null;
                $randomPassword = $generateRandomPassword();
                $hashedPassword = Hash::make($randomPassword);

                if ($data->hasFile('logo') && $data->file('logo')->isValid()) {
                    $image = $data->file('logo');
                    $filename = uniqid('logo_') . '.webp';
                    $path = 'public/logos/' . $filename;

                    // Obtener la extensión del archivo original
                    $extension = strtolower($image->getClientOriginalExtension());

                    if ($extension === 'webp') {
                        // Si ya es WebP, simplemente mover el archivo
                        $image->storeAs('public/logos', $filename);
                    } else {
                        // Si no es WebP, convertir la imagen
                        $sourceImage = $this->createImageFromFile($image->path());
                        if ($sourceImage) {
                            $fullPath = storage_path('app/' . $path);
                            imagewebp($sourceImage, $fullPath, 80);
                            imagedestroy($sourceImage);
                        } else {
                            // Manejar el error si no se puede crear la imagen
                            return null;
                        }
                    }

                    // Obtener la URL del archivo guardado
                    $logoUrl = Storage::url($path);
                }

                $rutaMulti = null;
                if ($data->hasFile('ruta_multi') && $data->file('ruta_multi')->isValid()) {
                    $file = $data->file('ruta_multi');
                    $mimeType = $file->getMimeType();

                    if (strpos($mimeType, 'image') !== false) {
                        // Es una imagen
                        $fileNamerutamulti = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                        $folder = 'imagenes';
                        $path = "public/$folder/$fileNamerutamulti";

                        $extensionrutamulti = strtolower($file->getClientOriginalExtension());
                        if ($extensionrutamulti === 'webp') {
                            // Si ya es WebP, simplemente mover el archivo
                            $file->storeAs("public/$folder", $fileNamerutamulti);
                        } else {
                            // Convertir a WebP
                            $sourceImagerutamulti = $this->createImageFromFile($file->path());
                            if ($sourceImagerutamulti) {
                                $fullPathrutamulti = storage_path('app/' . $path);
                                // Guardar la imagen como WebP
                                imagewebp($sourceImagerutamulti, $fullPathrutamulti, 80);
                                // Liberar memoria
                                imagedestroy($sourceImagerutamulti);
                            } else {
                                return response()->json(['message' => 'No se pudo procesar la imagen'], 400);
                            }
                        }

                        $rutaMulti = Storage::url($path);
                    } elseif ($mimeType === 'application/pdf') {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $folder = 'documentos';
                        $path = $file->storeAs("public/$folder", $fileName);
                        $rutaMulti = Storage::url($path);
                    } else {
                        return response()->json(['message' => 'Tipo de archivo no soportado para ruta_multi'], 400);
                    }
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
                    $hashedPassword,
                    $data['estado'] === 'true' ? 1 : 0,
                ]);

                if (empty($results)) {
                    throw new Exception('No se recibió respuesta del procedimiento almacenado');
                } else {
                    $email = $results[0]->email;
                    $rol = 'Aliado';
                    if ($email) {
                        // \Log::info("Intentando enviar correo a: " . $email);
                        Mail::to($email)->send(new NotificacionCrearUsuario($email, $rol, $randomPassword));
                    } else {
                        // \Log::warning("No se pudo enviar el correo porque $email está vacío");
                    }
                }

                $response = $results[0]->mensaje;
                $aliadoId = $results[0]->id;

                if ($response === 'El nombre del aliado ya se encuentra registrado' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    throw new Exception($response);
                }

                // Procesar el banner
                $bannerUrl = null;

                if ($data->hasFile('banner_urlImagen') && $data->file('banner_urlImagen')->isValid()) {
                    $imagebanner = $data->file('banner_urlImagen');
                    $filenamebanner = uniqid('banner_') . '.webp';
                    $pathbanner = 'public/banners/' . $filenamebanner;

                    $extensionbanner = strtolower($imagebanner->getClientOriginalExtension());
                    // Crear una imagen desde el archivo original
                    if ($extensionbanner === 'webp') {
                        // Si ya es WebP, simplemente mover el archivo
                        $imagebanner->storeAs('public/banners', $filenamebanner);
                    } else {
                        // Si no es WebP, convertir la imagen
                        $sourceImage = $this->createImageFromFile($imagebanner->path());
                        if ($sourceImage) {
                            $fullPath = storage_path('app/' . $pathbanner);
                            imagewebp($sourceImage, $fullPath, 80);
                            imagedestroy($sourceImage);
                        } else {
                            // Manejar el error si no se puede crear la imagen
                            return null;
                        }
                    }
                    $bannerUrl = Storage::url($pathbanner);
                }
                // $bannerPath = $data->file('banner_urlImagen')->store('public/banners');

                Banner::create([
                    'urlImagen' => $bannerUrl,
                    'estadobanner' => $data['banner_estadobanner'],
                    'id_aliado' => $aliadoId,
                ]);

                DB::commit();
                Log::info('Aliado y banner creados:', ['aliadoId' => $aliadoId, 'response' => $response]);

                return response()->json(['message' => 'Aliado creado exitosamente'], 201);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error al crear aliado y banner:', ['message' => $e->getMessage()]);
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    private function createImageFromFile($filePath)
    {
        // Obtiene la información de la imagen
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        // Crea la imagen a partir del tipo MIME
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/bmp':
                return imagecreatefrombmp($filePath);
            default:
                return false;
        }
    }

    public function crearBanner(Request $request)
    {
        // Verifica si el usuario tiene permisos
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 401);
        }

        // Verifica si se ha subido una imagen válida para el banner
        if (!$request->hasFile('urlImagen') || !$request->file('urlImagen')->isValid()) {
            return response()->json(['message' => 'Se requiere una imagen válida para el banner'], 400);
        }

        // Cuenta el número de banners existentes para el aliado
        $bannerCount = Banner::where('id_aliado', $request->id_aliado)->count();

        // Limita la cantidad de banners a 3
        if ($bannerCount >= 3) {
            return response()->json(['message' => 'Ya existen 3 banners para este aliado. Debe eliminar un banner antes de crear uno nuevo.'], 400);
        }

        $bannerUrl = null;

        // Si la imagen ya está en formato WebP, simplemente muévela
        if ($request->hasFile('urlImagen') && $request->file('urlImagen')->isValid()) {
            $image = $request->file('urlImagen');
            $filename = uniqid('banner_') . '.webp';
            $path = 'public/banners/' . $filename;

            $extension = strtolower($image->getClientOriginalExtension());


            if ($extension === 'webp') {
                // Si ya es WebP, simplemente mover el archivo
                $image->storeAs('public/banners', $filename);
            } else {
                // Crear una imagen desde el archivo original
                $sourceImage = $this->createImageFromFile($image->path());
                if ($sourceImage) {
                    // Guardar la imagen como WebP
                    $fullPath = storage_path('app/' . $path);
                    imagewebp($sourceImage, $fullPath, 80);
                    // Liberar memoria
                    imagedestroy($sourceImage);
                    // Obtener la URL del archivo guardado
                } else {
                    // Manejar el error si no se puede crear la imagen
                    return null;
                }
            }
            $bannerUrl = Storage::url($path);
        }

        // Crea el banner en la base de datos
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

                $file = $request->file('urlImagen');
                $fileName = Str::random(40) . '.webp';
                $path = 'public/banners/' . $fileName;

                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'webp') {
                    // Si ya es WebP, simplemente mover el archivo
                    $file->storeAs('public/banners', $fileName);
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
                        return response()->json(['message' => 'No se pudo procesar la imagen'], 400);
                    }
                }
                $banner->urlImagen = str_replace('public', 'storage', $path);
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
        // Verifica si el usuario tiene permisos
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        // Busca el banner por ID
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner no encontrado'], 404);
        }

        // Convierte la URL del banner para eliminarlo del almacenamiento
        $url = str_replace('storage', 'public', $banner->urlImagen);

        // Elimina el archivo del almacenamiento
        Storage::delete($url);

        // Elimina el registro del banner en la base de datos
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


            if ($request->hasFile('ruta_multi') && $request->file('ruta_multi')->isValid()) {
                $file = $request->file('ruta_multi');
                $mimeType = $file->getMimeType();

                if (strpos($mimeType, 'image') !== false) {
                    // Es una imagen
                    $folder = 'imagenes';
                    $fileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                    $path = "public/$folder/$fileName";

                    $extension = strtolower($file->getClientOriginalExtension());
                    if ($extension === 'webp') {
                        // Si ya es WebP, simplemente mover el archivo
                        $file->storeAs("public/$folder", $fileName);
                    } else {
                        // Convertir a WebP
                        $sourceImage = $this->createImageFromFile($file->path());
                        if ($sourceImage) {
                            $fullPath = storage_path('app/' . $path);
                            // Guardar la imagen como WebP
                            imagewebp($sourceImage, $fullPath, 80);
                            // Liberar memoria
                            imagedestroy($sourceImage);
                        } else {
                            return response()->json(['error' => 'No se pudo procesar la imagen'], 400);
                        }
                    }
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

                $file = $request->file('logo');
                $fileName = Str::random(40) . '.webp';
                $path = 'public/logos/' . $fileName;

                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'webp') {
                    // Si ya es WebP, simplemente mover el archivo
                    $file->storeAs('public/logos', $fileName);
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
                        return response()->json(['message' => 'No se pudo procesar la imagen'], 400);
                    }
                }
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
        // Busca el aliado junto con su relación de autenticación y tipo de dato usando el ID proporcionado en la solicitud
        $aliado = Aliado::with(['auth', 'tipoDato'])->find($request->input('id'));

        // Verifica si se encontró el aliado
        if ($aliado) {
            // Convierte el logo a formato Base64, si existe
            $logoBase64 = $aliado->logo ? 'data:image/png;base64,' . $aliado->logo : null;

            // Obtiene el estado del aliado, si está disponible
            $estado = $aliado->auth ? $aliado->auth->estado : null;

            // Obtiene el nombre del tipo de dato, si está disponible
            $tipoDato = $aliado->tipoDato ? $aliado->tipoDato->nombre : null;

            // Devuelve una respuesta JSON con la información del aliado
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
            // Si no se encuentra el aliado, devuelve un mensaje de error 404
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        // Verifica si el usuario tiene rol 3 o 1 (permiso para desactivar aliados)
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 1) {
            // Busca el aliado por ID
            $aliado = Aliado::find($id);
            if (!$aliado) {
                // Si no se encuentra el aliado, devuelve un mensaje de error 404
                return response()->json([
                    'message' => 'Aliado no encontrado',
                ], 404);
            }
            // Desactiva el usuario asociado al aliado
            $user = $aliado->auth;
            $user->estado = 0;
            $user->save();

            // Devuelve una respuesta de éxito
            return response()->json([
                'message' => 'Aliado desactivado',
            ], 200);
        }

        // Si el usuario no tiene permisos, devuelve un mensaje de error 403
        return response()->json([
            'message' => 'No tienes permisos para realizar esta acción'
        ], 403);
    }


    public function mostrarAsesorAliado(Request $request, $id)
    {
        try {
            // Verifica si el usuario tiene permiso para ver asesores
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene el estado desde la solicitud, por defecto 'Activo'
            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;

            // Busca el aliado por ID
            $aliado = Aliado::find($id);
            if (!$aliado) {
                // Si no se encuentra el aliado, devuelve un mensaje de error 404
                return response()->json(['message' => 'No se encontró ningún aliado con este ID'], 404);
            }

            // Obtiene los asesores asociados al aliado
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

            // Transforma los datos de los asesores
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

            // Devuelve la lista de asesores con su estado
            return response()->json($asesoresConEstado);
        } catch (Exception $e) {
            // Manejo de excepciones
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
            $accion = $request->input('accion');

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


            return response()->json(['message' => $mensaje], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function verEmprendedoresxEmpresa()
    {
        // Verifica si el usuario tiene rol 3 (permiso para ver emprendedores)
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        // Obtiene todos los emprendedores con sus empresas asociadas
        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();

        // Devuelve la lista de emprendedores con sus empresas en formato JSON
        return response()->json($emprendedoresConEmpresas);
    }

    public function asesoriasXmes($id)
    {
        try {
            // Verifica si el usuario tiene rol 3 (permiso para acceder a esta función)
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función.']);
            }

            // Obtiene el año actual
            $ano = date('Y');

            // Consulta las asesorías del aliado específico para el año actual
            $asesorias = Asesoria::where('id_aliado', $id)
                ->whereYear('fecha', $ano)
                ->selectRaw('MONTH(fecha) as mes, COUNT(*) as total') //Selecciona el mes y luego cuenta las asesorias
                ->groupBy('mes')
                ->get();

            // Devuelve las asesorías en formato JSON
            return response()->json($asesorias);
        } catch (Exception $e) {
            // Manejo de excepciones: devuelve un error en caso de que ocurra un problema
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
