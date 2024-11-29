<?php

namespace App\Services;

use App\Repositories\Empresa\EmpresaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpresaService
{

    protected $empresaRepository;

    public function __construct(EmpresaRepositoryInterface $empresaRepository)
    {
        $this->empresaRepository = $empresaRepository;
    }

    public function obtenerEmpresas()
    {
        return $this->empresaRepository->obtenerEmpresas();
    }

    public function obtenerEmpresasPorEmprendedor($docEmprendedor)
    {
        return $this->empresaRepository->obtenerEmpresasPorEmprendedor($docEmprendedor);
    }

    public function obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento)
    {
        return $this->empresaRepository->obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento);
    }

    public function crearEmpresa(array $data)
    {
        DB::beginTransaction();

        try {
            // Crear la empresa
            $empresa = $this->empresaRepository->crearEmpresa($data['empresa']);

            // Manejar apoyos si existen
            $apoyos = [];
            if (isset($data['apoyos']) && !empty($data['apoyos'])) {
                foreach ($data['apoyos'] as $apoyo) {
                    $nuevoApoyo = $this->empresaRepository->crearApoyo([
                        "documento" => $apoyo['documento'],
                        "nombre" => $apoyo['nombre'],
                        "apellido" => $apoyo['apellido'],
                        "cargo" => $apoyo['cargo'],
                        "telefono" => $apoyo['telefono'],
                        "celular" => $apoyo['celular'],
                        "email" => $apoyo['email'],
                        "id_tipo_documento" => $apoyo['id_tipo_documento'],
                        "id_empresa" => $empresa->documento,
                    ]);
                    $apoyos[] = $nuevoApoyo;
                }
            }

            DB::commit();

            return [
                'message' => 'Empresa creada exitosamente',
                'empresa' => $empresa,
                'apoyos' => $apoyos,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la empresa y los apoyos: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }


    public function actualizarEmpresa($documento, array $data)
    {
        return $this->empresaRepository->actualizarEmpresa($documento, $data);
    }
}
