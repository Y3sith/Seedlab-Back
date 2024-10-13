<?php

namespace App\Services;

use App\Models\Banner;
use App\Repositories\Banner\BannerRepositoryInterface;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    protected $bannerRepository;
    protected $imageService;

    public function __construct(BannerRepositoryInterface $bannerRepository, ImageService $imageService)
    {
        $this->bannerRepository = $bannerRepository;
        $this->imageService = $imageService;
    }

    /**
     * Crea un nuevo banner.
     *
     * @param array $data
     * @param UploadedFile $imageFile
     * @return array
     * @throws Exception
     */
    public function crearBanner(array $data, $imageFile): array
    {
        // Verifica si el usuario tiene permisos
        $userRol = Auth::user()->id_rol;
        if (!in_array($userRol, [1, 3])) {
            throw new Exception('No tienes permisos para realizar esta acción', 401);
        }

        // Verifica el número de banners existentes para el aliado
        $bannerCount = $this->bannerRepository->contarBannersPorAliado($data['id_aliado']);
        if ($bannerCount >= 3) {
            throw new Exception('Ya existen 3 banners para este aliado. Debe eliminar un banner antes de crear uno nuevo.', 400);
        }

        // Inicia una transacción de base de datos
        DB::beginTransaction();

        try {
            // Procesa y almacena la imagen
            $bannerUrl = $this->imageService->procesarImagen($imageFile, 'banners');

            // Crea el banner en la base de datos
            $banner = $this->bannerRepository->crearBanner([
                'urlImagen' => $bannerUrl,
                'estadobanner' => $data['estadobanner'],
                'id_aliado' => $data['id_aliado'],
            ]);

            // Loguea los datos del banner
            Log::info('Datos del banner creado:', $banner->toArray());

            // Confirma la transacción
            DB::commit();

            return [
                'message' => 'Banner creado exitosamente',
                'banner' => $banner,
            ];
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            throw $e;
        }
    }


    public function contarBannersPorAliado(int $id_aliado): int
    {
        return Banner::where('id_aliado', $id_aliado)->count();
    }

    public function traerBanners(int $status)
    {
        return Banner::where('estadobanner', $status)
            ->select('urlImagen', 'estadobanner')
            ->get();
    }

    public function traerBannersxaliado($id_aliado)
    {
        return Banner::where('id_aliado', $id_aliado)
            ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
            ->get();
    }

    public function traerBannersxID(int $id)
    {
        return Banner::where('id', $id)
            ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
            ->first();
    }

    /**
     * Edita un banner existente.
     *
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $imageFile
     * @return array
     * @throws Exception
     */
    public function editarBanner(int $id, array $data, $imageFile = null): array
    {
        // Verifica si el usuario tiene permisos
        $userRol = Auth::user()->id_rol;
        if (!in_array($userRol, [1, 3])) {
            throw new Exception('No tienes permisos para realizar esta acción', 401);
        }

        // Obtiene el banner existente
        $banner = $this->bannerRepository->traerBannersxID($id);
        if (!$banner) {
            throw new Exception('Banner no encontrado', 404);
        }

        // Actualiza la imagen si se proporciona una nueva
        if ($imageFile && $imageFile->isValid()) {
            // Elimina la imagen anterior
            Storage::delete(str_replace('storage', 'public', $banner->urlImagen));

            // Procesa y almacena la nueva imagen
            $bannerUrl = $this->imageService->procesarImagen($imageFile, 'banners');

            // Actualiza el banner
            $banner = $this->bannerRepository->editarBanner($id, [
                'urlImagen' => $bannerUrl,
                'estadobanner' => $data['estadobanner'] ?? $banner->estadobanner,
            ]);
        } else {
            // Solo actualiza el estado si se proporciona
            $banner = $this->bannerRepository->editarBanner($id, [
                'estadobanner' => $data['estadobanner'] ?? $banner->estadobanner,
            ]);
        }

        return [
            'message' => 'Banner editado exitosamente',
            'banner' => $banner
        ];
    }

    /**
     * Elimina un banner existente.
     *
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function eliminarBanner(int $id): array
    {
        // Verifica si el usuario tiene permisos
        $userRol = Auth::user()->id_rol;
        if (!in_array($userRol, [1, 3])) {
            throw new Exception('No tienes permisos para realizar esta acción', 401);
        }

        // Obtiene el banner existente
        $banner = $this->bannerRepository->traerBannersxID($id);
        if (!$banner) {
            throw new Exception('Banner no encontrado', 404);
        }

        // Elimina la imagen del almacenamiento
        Storage::delete(str_replace('storage', 'public', $banner->urlImagen));

        // Elimina el registro del banner
        $this->bannerRepository->eliminarBanner($id);

        return [
            'message' => 'Banner eliminado correctamente'
        ];
    }
}
