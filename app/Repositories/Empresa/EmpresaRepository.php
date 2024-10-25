<?php

namespace App\Repositories\Empresa;

use App\Models\ApoyoEmpresa;
use App\Models\Empresa;

class EmpresaRepository implements EmpresaRepositoryInterface
{

    public function obtenerEmpresas()
    {
        return Empresa::all();
    }


    public function obtenerEmpresasPorEmprendedor($docEmprendedor)
    {
        return Empresa::where('id_emprendedor', $docEmprendedor)->get(['documento', 'nombre']);
    }

    public function obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento)
    {
        $empresa = Empresa::with('apoyos') // Utilizamos la relación con apoyos
            ->where('id_emprendedor', $idEmprendedor)
            ->where('documento', $documento)
            ->first();

        if ($empresa) {
            return $empresa->toArray(); // Esto incluirá automáticamente los apoyos
        }

        return null;
    }


    public function crearEmpresa(array $data)
    {
        return Empresa::create($data);
    }

    public function actualizarEmpresa($documento, array $data)
    {
        $empresa = Empresa::find($documento);
        if ($empresa) {
            $empresa->update($data);
            return $empresa;
        }
        return null;
    }

    public function crearApoyo(array $data)
    {
        return ApoyoEmpresa::create($data);
    }
}
