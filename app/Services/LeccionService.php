<?php
namespace App\Services;

use App\Repositories\Leccion\LeccionRepositoryInterface;
use Exception;

class LeccionService
{
    protected $leccionRepository;

    public function __construct(LeccionRepositoryInterface $leccionRepository)
    {
        $this->leccionRepository = $leccionRepository;
    }

    public function crearLeccion(array $data)
    {
        $existingNivel = $this->leccionRepository->encontrarPorNombreYNivel($data['nombre'], $data['id_nivel']);
        if ($existingNivel) {
            throw new Exception('Ya existe una lección con este nombre para este nivel');
        }
        return $this->leccionRepository->crearLeccion($data);
    }

    public function obtenerLeccionesPorNivel($idNivel)
    {
        return $this->leccionRepository->obtenerLeccionesPorNivel($idNivel);
    }

    public function actualizarLeccion($id, array $data)
    {
        $leccion = $this->leccionRepository->encontrarPorId($id);
        if (!$leccion) {
            throw new Exception('Lección no encontrada');
        }
        return $this->leccionRepository->actualizarLeccion($id, $data);
    }
}
