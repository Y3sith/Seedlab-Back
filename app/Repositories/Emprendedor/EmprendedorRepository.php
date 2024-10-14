<?php

namespace App\Repositories\Emprendedor;

use App\Models\Emprendedor;
use App\Models\Empresa;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\Storage;

class EmprendedorRepository implements EmprendedorRepositoryInterface
{

    public function obtenerEmpresasPorEmprendedor($id_emprendedor)
    {
        return Empresa::where('id_emprendedor', $id_emprendedor)
            ->select('documento', 'nombre', 'correo', 'direccion', 'id_emprendedor')
            ->paginate();
    }

    public function encontrarEmprendedorPorDocumento($documento)
    {
        return Emprendedor::where('documento', $documento)->first();
    }

    public function actualizarEmprendedor($emprendedor, array $data)
    {
        if (isset($data['imagen_perfil']) && $data['imagen_perfil']->isValid()) {
            Storage::delete(str_replace('storage', 'public', $emprendedor->imagen_perfil));
            $path = $data['imagen_perfil']->store('public/fotoPerfil');
            $data['imagen_perfil'] = str_replace('public', 'storage', $path);
        }

        $emprendedor->update($data);
        return $emprendedor;
    }

    public function desactivarEmprendedor($documento)
    {
        $emprendedor = Emprendedor::where('documento', $documento)->first();
        if ($emprendedor && $emprendedor->auth) {
            $emprendedor->auth->estado = 0;
            $emprendedor->auth->save();
            $emprendedor->email_verified_at = null;
            $emprendedor->save();
        }
        return $emprendedor;
    }

    public function obtenerTiposDocumento()
    {
        return TipoDocumento::all();
    }
}
