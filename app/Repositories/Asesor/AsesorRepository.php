<?php

namespace App\Repositories\Asesor;

use App\Models\Asesor;
use Illuminate\Support\Facades\DB;
use App\Repositories\Asesor\AsesorRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AsesorRepository implements AsesorRepositoryInterface
{

    public function crearAsesor(array $params)
    {
        //Log::info('Parámetros enviados a sp_registrar_asesor:', $params);

        $result = DB::select('CALL sp_registrar_asesor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $params);

        //Log::info('Resultado del procedimiento almacenado:', $result);

        return $result;
    }



    public function buscarAsesorPorId($id)
    {
        return Asesor::find($id);
    }

    public function actualizarAsesor(Asesor $asesor, array $data)
    {
        $asesor->update($data);
        return $asesor;
    }

    public function buscarAsesoriasPorId($id)
    {
        return Asesor::find($id)->asesorias()->with('emprendedor', 'horarios')->get();
    }

    public function buscarAsesorConUbicacion($id)
    {
        return Asesor::where('asesor.id', $id)
            ->join('municipios', 'asesor.id_municipio', '=', 'municipios.id')
            ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
            ->join('users', 'asesor.id_autentication', '=', 'users.id')
            ->select(
                'asesor.id',
                'asesor.nombre',
                'asesor.apellido',
                'asesor.documento',
                'asesor.id_tipo_documento',
                'asesor.imagen_perfil',
                'asesor.direccion',
                'asesor.celular',
                'asesor.fecha_nac',
                'asesor.genero',
                'asesor.id_municipio',
                'departamentos.id as id_departamento',
                'asesor.id_autentication',
                'users.email',
                'users.estado'
            )
            ->first();
    }

    public function findByCelular($celular)
    {
        return Asesor::where('celular', $celular)->first();
    }

    public function updateAsesorAliado($asesor)
    {
        $asesor->save();
    }
}
