<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AliadoService;
use App\Services\BannerService;
use App\Services\AsesoriaService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\ValidationException;

class AliadoApiController extends Controller
{
    protected $aliadoService;
    protected $bannerService;
    protected $asesoriaService;

    public function __construct(
        AliadoService $aliadoService,
        BannerService $bannerService,
        AsesoriaService $asesoriaService
    ) {
        $this->aliadoService = $aliadoService;
        $this->bannerService = $bannerService;
        $this->asesoriaService = $asesoriaService;
    }

    /**
     * Función para el fanpage: Traer aliados activos.
     */
    public function traerAliadosActivos($status)
    {
        try {
            $aliados = $this->aliadoService->traerAliadosActivos($status);
            return response()->json($aliados, 200);
        } catch (Exception $e) {
            Log::error('Error en traerAliadosActivos:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener aliados activos'], 500);
        }
    }

    /**
     * Función para el perfil y el editar de aliados: Traer aliado por ID.
     */
    public function traerAliadoxId($id)
    {
        try {
            $aliado = $this->aliadoService->traerAliadoxId($id);
            if (!$aliado) {
                return response()->json(['error' => 'Aliado no encontrado'], 404);
            }

            return response()->json([
                'id' => $aliado->id,
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $aliado->logo ? $aliado->logo : null,
                'ruta_multi' => $aliado->ruta_multi ? $aliado->ruta_multi : null,
                'id_tipo_dato' => $aliado->id_tipo_dato,
                'urlpagina' => $aliado->urlpagina,
                'email' => $aliado->auth->email,
                'estado' => $aliado->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en traerAliadoxId:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener aliado'], 500);
        }
    }


    /**
     * Listar aliados para superadmin.
     */
    public function mostrarAliados(Request $request)
    {
        try {
            $estado = $request->input('estado', 'Activo');
            $aliados = $this->aliadoService->mostrarAliados($estado);

            $aliadosConEstado = $aliados->map(function ($aliado) {
                return [
                    'id' => $aliado->id,
                    'nombre' => $aliado->nombre,
                    'id_auth' => $aliado->auth->id,
                    'email' => $aliado->auth->email,
                    'estado' => $aliado->auth->estado == 1 ? 'Activo' : 'Inactivo'
                ];
            });

            return response()->json($aliadosConEstado, 200);
        } catch (Exception $e) {
            Log::error('Error en mostrarAliados:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al listar aliados'], 500);
        }
    }

    /**
     * Obtener todos los aliados sin autorización del middleware.
     */
    public function getAllAliados($id)
    {
        try {
            $aliado = $this->aliadoService->getAllAliados($id);
            if (!$aliado) {
                return response()->json(['error' => 'Aliado no encontrado'], 404);
            }

            return response()->json([
                'id' => $aliado->id,
                'logo' => $aliado->logo ? $aliado->logo : null,
                'ruta_multi' => $aliado->ruta_multi ? $aliado->ruta_multi : null,
                'urlpagina' => $aliado->urlpagina,
                'estado' => $aliado->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en getAllAliados:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener aliado'], 500);
        }
    }

    /**
     * Función para traer los banners activos para el fanpage.
     */
    public function traerBanners($status)
    {
        try {
            $banners = $this->bannerService->traerBanners($status);

            $bannersTransformados = $banners->map(function ($banner) {
                return [
                    'urlImagen' => $banner->urlImagen,
                    'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo'
                ];
            });

            return response()->json($bannersTransformados, 200);
        } catch (Exception $e) {
            Log::error('Error en traerBanners:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener banners'], 500);
        }
    }

    /**
     * Función para mostrar los banners de cada aliado.
     */
    public function traerBannersxaliado($id_aliado)
    {
        try {
            $banners = $this->bannerService->traerBannersxaliado($id_aliado);

            $bannersTransformados = $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'urlImagen' => $banner->urlImagen,
                    'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo'
                ];
            });

            return response()->json($bannersTransformados, 200);
        } catch (Exception $e) {
            Log::error('Error en traerBannersxaliado:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener banners del aliado'], 500);
        }
    }

    /**
     * Función para mostrar cada banner para editar.
     */
    public function traerBannersxID($id)
    {
        try {
            $banner = $this->bannerService->traerBannersxID($id);
            if (!$banner) {
                return response()->json(['error' => 'Banner no encontrado'], 404);
            }

            return response()->json([
                'id' => $banner->id,
                'urlImagen' => $banner->urlImagen,
                'estadobanner' => $banner->estadobanner == 1 ? 'Activo' : 'Inactivo',
                'id_aliado' => $banner->id_aliado,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en traerBannersxID:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener banner'], 500);
        }
    }

    /**
     * Función para crear un nuevo aliado.
     */
    public function crearAliado(Request $request)
    {
        try {
            //Log::info('Iniciando creación de aliado', ['request' => $request->except(['logoFile', 'ruta_multi', 'bannerFile'])]);

            // 1. Validar los datos entrantes
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'urlpagina' => 'required',
                'id_tipo_dato' => 'required|integer',
                'email' => 'required|email|unique:users,email',
                'estado' => 'required',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'ruta_multi' => 'required',
                'bannerFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ]);

            //Log::info('Validación exitosa');

            // 2. Extraer los datos relevantes del request
            $data = $request->only([
                'nombre',
                'descripcion',
                'id_tipo_dato',
                'ruta_multi',
                'urlpagina',
                'email',
                'estado',
            ]);

            // 3. Extraer los archivos
            $logoFile = $request->file('logo');
            $rutaMultiFile = $request->file('ruta_multi');
            $bannerFile = $request->file('bannerFile');

            //Log::info('Llamando al servicio crearAliado');

            // 4. Llamar al método del servicio con los argumentos correctos
            $resultado = $this->aliadoService->crearAliado($data, $logoFile, $rutaMultiFile, $bannerFile);

            //Log::info('Aliado creado exitosamente', ['resultado' => $resultado]);

            // 5. Retornar la respuesta exitosa
            return response()->json([
                'message' => $resultado['message'],
                'aliadoId' => $resultado['aliadoId']
            ], 201);
        } catch (ValidationException $ve) {
            Log::error('Error de validación', ['errors' => $ve->errors()]);
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error en crearAliado', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error al crear aliado: ' . $e->getMessage()], 400);
        }
    }


    /**
     * Función para crear un nuevo banner.
     */
    public function crearBanner(Request $request)
    {
        try {
            //Log::info('Iniciando creación de banner', ['request' => $request->all()]);

            // 1. Validar los datos entrantes
            $validatedData = $request->validate([
                'urlImagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'estadobanner' => 'required|boolean',
                'id_aliado' => 'required|integer|exists:aliado,id',

            ]);

            //Log::info('Validación exitosa', ['validatedData' => $validatedData]);

            // 2. Extraer los datos relevantes del request
            $data = $request->only([
                'urlImagen',
                'estadobanner',
                'id_aliado',
            ]);

            //Log::info('Datos relevantes extraídos', ['data' => $data]);

            // 3. Extraer el archivo de imagen
            $imageFile = $request->file('urlImagen');
            Log::info('Archivo de imagen extraído', ['urlImagen' => $imageFile]);

            // 4. Llamar al método del servicio con los argumentos correctos
            $resultado = $this->bannerService->crearBanner($data, $imageFile);

            //Log::info('Banner creado exitosamente en el servicio', ['resultado' => $resultado]);

            // 5. Retornar la respuesta exitosa
            return response()->json([
                'message' => $resultado['message'],
                'banner' => $resultado['banner']
            ], 201);
        } catch (ValidationException $ve) {
            Log::error('Error de validación', ['errors' => $ve->errors()]);
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error al crear banner', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al crear banner: ' . $e->getMessage()
            ], 400);
        }
    }



    /**
     * Función para editar un banner existente.
     */
    public function editarBanner(Request $request, $id)
    {
        try {
            // 1. Validar los datos entrantes
            $validatedData = $request->validate([
                'urlImagen' => 'nullable|url',
                'estadobanner' => 'nullable|boolean',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Ajusta el nombre del campo según tu formulario
            ]);

            // 2. Extraer los datos relevantes del request
            $data = $request->only([
                'urlImagen',
                'estadobanner',
            ]);

            // 3. Extraer el archivo de imagen si está presente
            $imageFile = $request->file('imageFile'); // Asegúrate de que el nombre del campo coincide con tu formulario

            // 4. Llamar al método del servicio con los argumentos correctos
            $resultado = $this->bannerService->editarBanner($id, $data, $imageFile);

            // 5. Retornar la respuesta exitosa
            return response()->json([
                'message' => $resultado['message'],
                'banner' => $resultado['banner']
            ], 200);
        } catch (ValidationException $ve) {
            // Manejo de errores de validación
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            // Manejo de otros errores
            Log::error('Error en editarBanner:', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al editar banner: ' . $e->getMessage()
            ], 400);
        }
    }


    /**
     * Función para eliminar un banner existente.
     */
    public function eliminarBanner($id)
    {
        try {
            $this->bannerService->eliminarBanner($id);
            return response()->json(['message' => 'Banner eliminado correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error en eliminarBanner:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Error al eliminar banner: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Función para editar un aliado existente.
     */
    public function editarAliado(Request $request, $id)
    {
        try {
            //Log::info('Iniciando edición de aliado', ['id' => $id, 'request_data' => $request->all()]);

            // Validación de los datos entrantes
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'id_tipo_dato' => 'required|integer|exists:tipo_dato,id',
                'ruta_multi' => 'nullable|string',
                'urlpagina' => 'nullable',
                'email' => 'nullable|email',
                'password' => 'nullable|string|min:8',
                'estado' => 'nullable',
                'logo' => 'nullable',
                'ruta_multi_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:5120'
            ]);

            //Log::info('Validación de datos exitosa', ['validated_data' => $validatedData]);

            // Extraer los datos relevantes
            $data = $request->only([
                'nombre',
                'descripcion',
                'id_tipo_dato',
                'ruta_multi',
                'urlpagina',
                'email',
                'password',
                'estado'
            ]);

            //Log::info('Datos extraídos para actualización', ['data' => $data]);

            // Extraer los archivos si están presentes
            $logoFile = $request->file('logo');
            $rutaMultiFile = $request->file('ruta_multi_file');

            // Log::info('Archivos extraídos', [
            //     'logoFile' => $logoFile ? $logoFile->getClientOriginalName() : 'No file',
            //     'rutaMultiFile' => $rutaMultiFile ? $rutaMultiFile->getClientOriginalName() : 'No file'
            // ]);

            // Llamar al servicio para editar el aliado
            $resultado = $this->aliadoService->editarAliado($id, $data, $logoFile, $rutaMultiFile);

            //Log::info('Resultado del servicio editarAliado', ['resultado' => $resultado]);

            return response()->json([
                'message' => $resultado['message'],
                'aliado' => $resultado['aliado']
            ], 200);
        } catch (ValidationException $ve) {
            Log::warning('Error de validación en editarAliado', ['errors' => $ve->errors()]);
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error en editarAliado:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error al actualizar aliado: ' . $e->getMessage()
            ], 500);
        }
    }




    /**
     * Función para desactivar un aliado.
     */
    public function destroy($id)
    {
        try {
            $this->aliadoService->desactivarAliado($id);
            return response()->json(['message' => 'Aliado desactivado'], 200);
        } catch (Exception $e) {
            Log::error('Error en destroy:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al desactivar aliado: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Función para gestionar asesorías.
     */
    public function gestionarAsesoria(Request $request)
    {
        try {
            $asesoriaId = $request->input('id_asesoria');
            $accion = $request->input('accion');

            $this->asesoriaService->gestionarAsesoria($asesoriaId, $accion);

            return response()->json(['message' => 'Asesoría gestionada correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error en gestionarAsesoria:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Error al gestionar asesoría: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Función para mostrar asesores de un aliado.
     */
    public function mostrarAsesorAliado(Request $request, $id)
    {
        try {
            // Verifica permisos de usuario
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Obtiene el estado desde la solicitud
            $estado = $request->input('estado', 'Activo');
            //Log::info('Mostrando asesores para aliado', ['id' => $id, 'estado' => $estado]);

            // Llama al servicio para obtener los asesores
            $asesoresConEstado = $this->aliadoService->mostrarAsesoresPorAliado($id, $estado);

            return response()->json($asesoresConEstado);
        } catch (Exception $e) {
            Log::error('Error en mostrarAsesorAliado:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Función para desactivar un aliado.
     * Nota: Esta función ya ha sido implementada anteriormente como `destroy`.
     * Puedes eliminarla si es redundante.
     */
    public function eliminarAliado($id)
    {
        try {
            $this->aliadoService->desactivarAliado($id);
            return response()->json(['message' => 'Aliado desactivado'], 200);
        } catch (Exception $e) {
            Log::error('Error en eliminarAliado:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al desactivar aliado: ' . $e->getMessage()], 400);
        }
    }
}
