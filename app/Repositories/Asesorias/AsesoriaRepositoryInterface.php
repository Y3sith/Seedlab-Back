<?php

namespace App\Repositories\Asesorias;


interface AsesoriaRepositoryInterface
{
    public function gestionarAsesoria(int $id_asesoria, string $accion);
    // Agrega otros métodos según necesidad
}
