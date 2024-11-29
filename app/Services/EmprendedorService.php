<?php

namespace App\Services;

use App\Repositories\Emprendedor\EmprendedorRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmprendedorService
{
    protected $emprendedorRepository;

    public function __construct(EmprendedorRepositoryInterface $emprendedorRepository)
    {
        $this->emprendedorRepository = $emprendedorRepository;
    }

    public function obtenerEmpresas($id_emprendedor)
    {
        $empresas = $this->emprendedorRepository->obtenerEmpresasPorEmprendedor($id_emprendedor);
        if ($empresas->isEmpty()) {
            throw new Exception('Empresa no encontrada', 404);
        }
        return $empresas->items();
    }

    public function actualizarEmprendedor($documento, array $data)
    {
        $emprendedor = $this->emprendedorRepository->encontrarEmprendedorPorDocumento($documento);
        if (!$emprendedor) {
            throw new Exception('El emprendedor no fue encontrado', 404);
        }

        if (isset($data['password'])) {
            $emprendedor->load('auth');
            if ($emprendedor->auth) {
                $user = $emprendedor->auth;
                if (Hash::check($data['password'], $user->password)) {
                    throw new Exception('La nueva contraseña no puede ser igual a la actual', 400);
                }
                $user->password = Hash::make($data['password']);
                $user->save();
            } else {
                throw new Exception('No se encontró un usuario asociado al emprendedor', 404);
            }
            unset($data['password']);
        }

        return $this->emprendedorRepository->actualizarEmprendedor($emprendedor, $data);
    }

    public function desactivarEmprendedor($documento)
    {
        $emprendedor = $this->emprendedorRepository->desactivarEmprendedor($documento);
        if (!$emprendedor) {
            throw new Exception('Emprendedor no encontrado', 404);
        }
        return $emprendedor;
    }

    public function obtenerTiposDocumento()
    {
        return $this->emprendedorRepository->obtenerTiposDocumento();
    }
}
