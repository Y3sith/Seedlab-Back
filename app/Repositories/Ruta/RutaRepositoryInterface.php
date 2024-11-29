<?php

namespace App\Repositories\Ruta;

interface RutaRepositoryInterface
{
    public function obtenerRutasPorEstado($estado);
    public function obtenerRutaPorId($id);
    public function crearRuta(array $data);
    public function obtenerRutasActivas();
    public function obtenerRutaPorNombre($nombreRuta);
    public function obtenerRutas();
    public function actualizarRuta($id, array $data);
    public function desactivarRuta($id);
    public function obtenerActividadesConEstado($idRuta, $estado);
    public function obtenerActividadesPorRutaYAliado($idRuta, $idAliado, $estado);
    public function obtenerActividadesPorNivelYAsesor($idRuta, $idAsesor, $estado);
    public function obtenerRutaConActividades($idRuta);
    public function obtenerContenidoPorId($id);
    public function obtenerRutaConRelaciones($id);
    public function obtenerEmpresasPorEmprendedor($idEmprendedor);
}