<?php

namespace App\Repositories\Asesor;

use App\Models\Asesor;

interface AsesorRepositoryInterface {
    public function crearAsesor(array $data);
    public function buscarAsesorPorId($id);
    public function actualizarAsesor(Asesor $asesor, array $data);
    public function buscarAsesoriasPorId($id);
    public function buscarAsesorConUbicacion($id);

    public function findByCelular($celular);
    public function updateAsesorAliado($asesor);
}
