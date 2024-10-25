<?php

namespace App\Repositories\Asesor;

use App\Models\Asesor;
use Illuminate\Support\Facades\DB;
use App\Repositories\Asesor\AsesorRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AsesorRepository implements AsesorRepositoryInterface
{

    //Crear un nuevo asesor en la base de datos utilizando un procedimiento almacenado.
    public function crearAsesor(array $params)
    {
        //Log::info('Parámetros enviados a sp_registrar_asesor:', $params);

        // Ejecuta el procedimiento almacenado 'sp_registrar_asesor' con los parámetros proporcionados.
        $result = DB::select('CALL sp_registrar_asesor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $params);

        //Log::info('Resultado del procedimiento almacenado:', $result);

        return $result;
    }

    //Buscar un asesor por su ID.
    public function buscarAsesorPorId($id)
    {
        // Encuentra y retorna un asesor utilizando el método find, basado en su ID.
        return Asesor::find($id);
    }

    //Actualizar los datos de un asesor.
    public function actualizarAsesor(Asesor $asesor, array $data)
    {
        // Actualiza los datos del asesor con el array de datos proporcionado.
        $asesor->update($data);
        return $asesor;
    }

    //Buscar asesorías relacionadas a un asesor por su ID.
    public function buscarAsesoriasPorId($id)
    {
        return Asesor::find($id)->asesorias()->with('emprendedor', 'horarios')->get();
    }

    //Buscar un asesor junto con su ubicación (municipio y departamento).
    public function buscarAsesorConUbicacion($id)
    {
        // Realiza una búsqueda en la tabla asesor y une con las tablas de municipios, departamentos y usuarios,
        // seleccionando varios campos para obtener toda la información relacionada con el asesor.
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
            ->first();// Retorna el primer resultado encontrado.
    }

    //Buscar un asesor por su número de celular.
    public function findByCelular($celular)
    {
        return Asesor::where('celular', $celular)->first();
    }

    //Actualizar el estado o datos relacionados con el aliado de un asesor.
    public function updateAsesorAliado($asesor)
    {
        $asesor->save();
    }
}
