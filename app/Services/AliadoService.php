<?php

namespace App\Services;


use App\Repositories\Banner\BannerRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Jobs\EnviarNotificacionCrearUsuario;
use App\Models\User;
use App\Repositories\Aliado\AliadoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AliadoService
{
    protected $aliadoRepository;
    protected $imageService;
    protected $bannerRepository;

    public function __construct(AliadoRepositoryInterface $aliadoRepository, ImageService $imageService, BannerRepositoryInterface $bannerRepository)
    {
        $this->aliadoRepository = $aliadoRepository;
        $this->imageService = $imageService;
        $this->bannerRepository = $bannerRepository;
    }

    public function traerAliadosActivos(int $status)
    {
        return $this->aliadoRepository->traerAliadosActivos($status);
    }

    public function traerAliadoxId(int $id)
    {
        return $this->aliadoRepository->traerAliadoxId($id);
    }

    public function mostrarAliados(string $estado)
    {
        return $this->aliadoRepository->mostrarAliados($estado);
    }

    public function getAllAliados(int $id)
    {
        return $this->aliadoRepository->getAllAliados($id);
    }

    public function desactivarAliado($id)
    {
        return $this->aliadoRepository->desactivarAliado($id);
    }

    public function mostrarAsesorAliado(string $estado)
    {
        return $this->aliadoRepository->mostrarAliados($estado);
    }

    /**
     * Crea un nuevo aliado.
     */
    public function crearAliado(array $data, $logoFile, $rutaMultiFile = null, $bannerFile = null): array
    {
        DB::beginTransaction();

        try {
            Log::info('Iniciando creación de aliado en el servicio', ['data' => $data]);

            // 1. Generar contraseña aleatoria y su hash
            $randomPassword = $this->generateRandomPassword();
            $hashedPassword = Hash::make($randomPassword);

            // 2. Procesar logo
            Log::info('Procesando logo');
            $logoUrl = $this->imageService->procesarImagen($logoFile, 'logos');
            Log::info('Logo procesado', ['logoUrl' => $logoUrl]);

            // 3. Procesar ruta_multi si es un archivo subido o un enlace de video
            if ($rutaMultiFile instanceof UploadedFile) {
                // Caso cuando es un archivo subido
                Log::info('Procesando ruta_multi como archivo');
                $rutaMulti = $this->imageService->procesarImagen($rutaMultiFile, 'ruta_multi');
                Log::info('Ruta multi procesada como archivo', ['ruta_multi' => $rutaMulti]);

                // Seleccionamos la versión 'medium' de la imagen
                $rutaMultiSeleccionada = $rutaMulti['medium'];
            } elseif (is_string($data['ruta_multi']) && filter_var($data['ruta_multi'], FILTER_VALIDATE_URL)) {
                // Caso cuando es un enlace de video
                Log::info('Procesando ruta_multi como un enlace de video');
                $rutaMultiSeleccionada = $data['ruta_multi'];  // Aquí usas directamente el enlace de video
            } else {
                // Si no es un archivo válido ni un enlace de video
                throw new Exception("El campo 'ruta_multi' no es un archivo válido ni un enlace de video.");
            }

            Log::info('Ruta multi procesada', ['ruta_multi' => $rutaMultiSeleccionada]);

            // 4. Preparar datos para el procedimiento almacenado
            $aliadoData = [
                'nombre' => $data['nombre'],
                'logoUrl' => $logoUrl,
                'descripcion' => $data['descripcion'],
                'id_tipo_dato' => $data['id_tipo_dato'],
                'ruta_multi' => $rutaMultiSeleccionada,  // Usamos la imagen o el enlace de video
                'urlpagina' => $data['urlpagina'],
                'email' => $data['email'],
                'hashedPassword' => $hashedPassword,
                'estado' => $data['estado'],
            ];

            // 5. Crear Aliado usando el Repositorio
            Log::info('Creando aliado en el repositorio');
            $result = $this->aliadoRepository->crearAliado($aliadoData);
            Log::info('Respuesta del repositorio', ['result' => $result]);

            // 6. Manejar la respuesta del procedimiento almacenado
            $response = $result->mensaje;
            $aliadoId = $result->id;

            if (in_array($response, [
                'El nombre del aliado ya se encuentra registrado',
                'El correo electrónico ya ha sido registrado anteriormente'
            ])) {
                throw new Exception($response);
            }

            // 7. Enviar correo usando Job
            if ($result->email) {
                Log::info('Despachando job para enviar notificación', ['email' => $result->email]);
                EnviarNotificacionCrearUsuario::dispatch($result->email, 'Aliado', $randomPassword);
            }

            // 8. Procesar banner si se proporciona
            // Si hay un banner, procesarlo
            if ($bannerFile && $bannerFile->isValid()) {
                Log::info('Procesando banner');
                $bannerUrls = $this->imageService->procesarImagen($bannerFile, 'banners');
                Log::info('Banner procesado', ['bannerUrls' => $bannerUrls]);

                // Preparar los datos del banner
                $bannerData = [
                    'urlImagenSmall' => $bannerUrls['small'],
                    'urlImagenMedium' => $bannerUrls['medium'],
                    'urlImagenLarge' => $bannerUrls['large'],
                    'estadobanner' => $data['banner_estadobanner'] ?? true,
                    'id_aliado' => $result->id, // Asociar con el aliado recién creado
                ];

                Log::info('Creando banner en el repositorio');
                $this->bannerRepository->crearBanner($bannerData); // Usar el repositorio inyectado
            }
            DB::commit();
            Log::info('Transacción completada, aliado creado exitosamente');

            return ['message' => 'Aliado creado exitosamente', 'aliadoId' => $aliadoId];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en crearAliado del servicio', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * 
     *Edita un aliado existente.
     */
    public function editarAliado(int $id, array $data, $logoFile = null, $rutaMultiFile = null): array
    {
        DB::beginTransaction();

        try {
            // Editar el aliado usando el Repositorio
            $aliado = $this->aliadoRepository->editarAliado($id, $data);

            // Actualizar logo si se proporciona
            if ($logoFile && $logoFile->isValid()) {
                // Procesar nueva imagen de logo
                $logoUrl = $this->imageService->procesarImagen($logoFile, 'logos');
                $aliado->logo = $logoUrl;
                $aliado->save();
            }

            // Actualizar ruta_multi si se proporciona
            if ($rutaMultiFile && $rutaMultiFile->isValid()) {
                $rutaMulti = $this->procesarRutaMulti($data, $rutaMultiFile);
                $aliado->ruta_multi = $rutaMulti;
                $aliado->save();
            }

            // Actualizar los datos del usuario asociado
            $user = $aliado->auth;

            // Actualizar email si se proporciona y es diferente
            if (isset($data['email']) && $data['email'] !== $user->email) {
                // Verificar si el nuevo email ya está en uso
                if (User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
                    throw new Exception('El correo electrónico ya ha sido registrado anteriormente');
                }
                $user->email = $data['email'];
            }

            // Actualizar la contraseña si se proporciona
            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            // Actualizar el estado si se proporciona
            if (isset($data['estado'])) {
                $user->estado = filter_var($data['estado'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }

            $user->save();

            DB::commit();

            return ['message' => 'Aliado actualizado exitosamente', 'aliado' => $aliado];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en editarAliado del servicio:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * Procesa el campo 'ruta_multi' según el tipo de dato.
     */
    protected function procesarRutaMulti(array $data, $rutaMultiFile = null)
    {
        if (in_array($data['id_tipo_dato'], [2, 3])) {
            // Procesar archivo
            if (!$rutaMultiFile || !$rutaMultiFile->isValid()) {
                throw new Exception('Se requiere un archivo válido para ruta_multi');
            }
            return $this->imageService->procesarImagen($rutaMultiFile, 'ruta_multi');
        } elseif (in_array($data['id_tipo_dato'], [1, 4])) {
            // Guardar texto
            return trim($data['ruta_multi']);
        }

        return null;
    }

    /**
     * Genera una contraseña aleatoria.
     */
    protected function generateRandomPassword(int $length = 8): string
    {
        return Str::random($length);
    }

    public function mostrarAsesoresPorAliado(int $id, string $estado): array
    {
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        // Llama al repositorio para obtener los asesores según el estado
        $asesores = $this->aliadoRepository->obtenerAsesoresPorAliado($id, $estadoBool);

        // Transforma los datos de los asesores
        $asesoresConEstado = $asesores->map(function ($asesor) {
            $user = User::find($asesor->id_autentication);
            return [
                'id' => $asesor->id,
                'nombre' => $asesor->nombre,
                'apellido' => $asesor->apellido,
                'imagen_perfil' => $asesor->imagen_perfil ? $asesor->imagen_perfil : null,
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
        })->toArray();

        return $asesoresConEstado;
    }
}
