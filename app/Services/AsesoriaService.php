<?php

namespace App\Services;

use App\Repositories\Asesorias\AsesoriaRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class AsesoriaService
{
    protected $asesoriaRepository;

    public function __construct(AsesoriaRepositoryInterface $asesoriaRepository)
    {
        $this->asesoriaRepository = $asesoriaRepository;
    }

    /**
     * Gestiona una asesoría (rechazar).
     *
     * @param int $id_asesoria
     * @param string $accion
     * @return array
     * @throws Exception
     */
    public function gestionarAsesoria(int $id_asesoria, string $accion): array
    {
        // Verifica si el usuario tiene permisos
        $userRol = Auth::user()->id_rol;
        if (!in_array($userRol, [1, 3])) {
            throw new Exception('No tienes permisos para realizar esta acción', 401);
        }

        // Gestiona la asesoría
        $mensaje = $this->asesoriaRepository->gestionarAsesoria($id_asesoria, $accion);

        return [
            'message' => $mensaje
        ];
    }
}
