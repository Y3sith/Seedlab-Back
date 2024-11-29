<?php

namespace App\Repositories\Banner;

interface BannerRepositoryInterface
{
    public function crearBanner(array $data);
    public function contarBannersPorAliado(int $id_aliado): int;
    public function traerBanners(int $status);
    public function traerBannersxaliado(int $id_aliado);
    public function traerBannersxID(int $id);
    public function editarBanner(int $id, array $data);
    public function eliminarBanner(int $id);
}
