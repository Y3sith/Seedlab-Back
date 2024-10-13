<?php

namespace App\Services;


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

    public function __construct(AliadoRepositoryInterface $aliadoRepository, ImageService $imageService)
    {
        $this->aliadoRepository = $aliadoRepository;
        $this->imageService = $imageService;
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
     *
     * @param array $data
     * @param UploadedFile $logoFile
     * @param UploadedFile|null $rutaMultiFile
     * @param UploadedFile|null $bannerFile
     * @return array
     * @throws Exception
     */
    public function crearAliado(array $data, $logoFile, $rutaMultiFile = null, $bannerFile = null): array
    {
        DB::beginTransaction();

        try {
            //Log::info('Iniciando creación de aliado en el servicio', ['data' => $data]);

            // 1. Generar contraseña aleatoria y su hash
            $randomPassword = $this->generateRandomPassword();
            $hashedPassword = Hash::make($randomPassword);

            // 2. Procesar logo
            //Log::info('Procesando logo');
            $logoUrl = $this->imageService->procesarImagen($logoFile, 'logos');
            //Log::info('Logo procesado', ['logoUrl' => $logoUrl]);

            // 3. Procesar ruta_multi según tipo
            //Log::info('Procesando ruta_multi');
            $rutaMulti = $this->procesarRutaMulti($data, $rutaMultiFile);
            //Log::info('Ruta multi procesada', ['ruta_multi' => $rutaMulti]);

            // 4. Preparar datos para el procedimiento almacenado
            $aliadoData = [
                'nombre' => $data['nombre'],
                'logoUrl' => $logoUrl,
                'descripcion' => $data['descripcion'],
                'id_tipo_dato' => $data['id_tipo_dato'],
                'ruta_multi' => $rutaMulti,
                'urlpagina' => $data['urlpagina'],
                'email' => $data['email'],
                'hashedPassword' => $hashedPassword,
                'estado' => $data['estado'],
            ];

            // 5. Crear Aliado usando el Repositorio
            //Log::info('Creando aliado en el repositorio');
            $result = $this->aliadoRepository->crearAliado($aliadoData);
            //Log::info('Respuesta del repositorio', ['result' => $result]);

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
            if ($bannerFile && $bannerFile->isValid()) {
                //Log::info('Procesando banner');
                $bannerUrl = $this->imageService->procesarImagen($bannerFile, 'banners');
                //Log::info('Banner procesado', ['bannerUrl' => $bannerUrl]);

                // Crear Banner usando el Repositorio
                $bannerData = [
                    'urlImagen' => $bannerUrl,
                    'estadobanner' => $data['banner_estadobanner'] ?? true,
                    'id_aliado' => $aliadoId,
                ];
                //Log::info('Creando banner');
                app('App\Repositories\BannerRepositoryInterface')->crearBanner($bannerData);
            }

            DB::commit();
            //Log::info('Transacción completada, aliado creado exitosamente');

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
     * Edita un aliado existente.
     *
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $logoFile
     * @param UploadedFile|null $rutaMultiFile
     * @return array
     * @throws Exception
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
     *
     * @param array $data
     * @param UploadedFile|null $rutaMultiFile
     * @return string|null
     * @throws Exception
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
     *
     * @param int $length
     * @return string
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
