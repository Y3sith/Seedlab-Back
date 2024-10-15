<?php

namespace App\Services;

use App\Repositories\Orientador\OrientadorRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class OrientadorService{

    protected $orientadorRepository;

    public function __construct(OrientadorRepositoryInterface $orientadorRepository)
    {
        $this->orientadorRepository = $orientadorRepository;
    }

    public function crearOrientador(array $data)
    {
        $data['random_password'] = bin2hex(random_bytes(4));
        $data['hashed_password'] = Hash::make($data['random_password']);

        return $this->orientadorRepository->crearOrientador($data);
    }

    public function asignarAsesoriaAliado($idAsesoria, $nombreAliado)
    {
        return $this->orientadorRepository->asignarAsesoriaAliado($idAsesoria, $nombreAliado);
    }

    public function listarAliados()
    {
        return $this->orientadorRepository->listarAliados();
    }

    public function contarEmprendedores()
    {
        return $this->orientadorRepository->contarEmprendedores();
    }

    public function mostrarOrientadores($status)
    {
        return $this->orientadorRepository->mostrarOrientadores($status);
    }

    public function editarOrientador($id, array $data)
    {
        return $this->orientadorRepository->editarOrientador($id, $data);
    }

    public function obtenerPerfil($id)
    {
        return $this->orientadorRepository->obtenerPerfil($id);
    }
}