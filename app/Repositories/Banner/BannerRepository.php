<?php

namespace App\Repositories\Banner;

use App\Models\Banner;
use Exception;

class BannerRepository implements BannerRepositoryInterface
{
    public function crearBanner(array $data)
    {
        return Banner::create([
            'urlImagen' => $data['urlImagen'],
            'estadobanner' => $data['estadobanner'],
            'id_aliado' => $data['id_aliado'],
        ]);
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

    public function traerBannersxaliado(int $id_aliado)
    {
        return Banner::where('id_aliado', $id_aliado)
            ->select('id', 'urlImagen', 'estadobanner', 'id_aliado')
            ->get();
    }

    public function traerBannersxID(int $id)
    {
        return Banner::find($id);
    }

    public function editarBanner(int $id, array $data)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            throw new Exception('Banner no encontrado');
        }

        $banner->urlImagen = $data['urlImagen'] ?? $banner->urlImagen;
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
