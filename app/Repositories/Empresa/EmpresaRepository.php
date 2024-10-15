<?php

namespace App\Repositories\Empresa;

use App\Models\ApoyoEmpresa;
use App\Models\Empresa;

class EmpresaRepository implements EmpresaRepositoryInterface{

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
        $empresa = Empresa::where('id_emprendedor', $idEmprendedor)
            ->where('documento', $documento)
            ->first();

        if ($empresa) {
            $apoyo = ApoyoEmpresa::where('id_empresa', $empresa->documento)->first();
            $data = $empresa->toArray();
            $data['apoyo'] = $apoyo;
            return $data;
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
}