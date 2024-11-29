<?php

namespace App\Repositories\Banner;

use App\Models\Banner;
use Exception;

class BannerRepository implements BannerRepositoryInterface
{
    public function crearBanner(array $data)
    {
        return Banner::create([
            'urlImagenSmall' => $data['urlImagenSmall'],  // Almacena la versión pequeña
            'urlImagenMedium' => $data['urlImagenMedium'],  // Almacena la versión mediana
            'urlImagenLarge' => $data['urlImagenLarge'],  // Almacena la versión grande
            'estadobanner' => $data['estadobanner'],  // Estado del banner
            'id_aliado' => $data['id_aliado'],  // Relación con el aliado
        ]);
    }


    public function contarBannersPorAliado(int $id_aliado): int
    {
        return Banner::where('id_aliado', $id_aliado)->count();
    }

    public function traerBanners(int $status)
    {
        return Banner::where('estadobanner', $status)
            ->select('id', 'urlImagenSmall', 'urlImagenMedium', 'urlImagenLarge', 'estadobanner', 'id_aliado')
            ->get();
    }

    public function traerBannersxaliado(int $id_aliado)
    {
        return Banner::where('id_aliado', $id_aliado)
            ->select('id', 'urlImagenSmall', 'urlImagenMedium', 'urlImagenLarge', 'estadobanner', 'id_aliado')
            ->get();
    }


    public function traerBannersxID(int $id)
    {
        return Banner::select('id', 'urlImagenSmall', 'urlImagenMedium', 'urlImagenLarge', 'estadobanner', 'id_aliado')
            ->where('id', $id)
            ->first();
    }


    public function editarBanner(int $id, array $data)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            throw new Exception('Banner no encontrado');
        }

        // Actualizar las imágenes si existen en los datos
        $banner->urlImagenSmall = $data['urlImagenSmall'] ?? $banner->urlImagenSmall;
        $banner->urlImagenMedium = $data['urlImagenMedium'] ?? $banner->urlImagenMedium;
        $banner->urlImagenLarge = $data['urlImagenLarge'] ?? $banner->urlImagenLarge;

        // Actualizar el estado del banner
        $banner->estadobanner = $data['estadobanner'] ?? $banner->estadobanner;

        $banner->save();

        return $banner;
    }


    public function eliminarBanner(int $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            throw new Exception('Banner no encontrado');
        }

        $banner->delete();

        return true;
    }
}
