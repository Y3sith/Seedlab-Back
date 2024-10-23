<?php


namespace App\Repositories\Orientador;

use App\Models\Orientador;
use App\Models\Aliado;
use App\Models\Asesoria;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrientadorRepository implements OrientadorRepositoryInterface
{

    public function crearOrientador(array $data)
    {
        $plainPassword = $data['random_password'];
        $hashedPassword = $data['hashed_password'];
        $direccion = $data['direccion'] ?? 'Dirección por defecto';
        $fecha_nac = $data['fecha_nac'] ?? '2000-01-01';
        $imagen_perfil = $data['imagen_perfil'] ?? '';

        return DB::transaction(function () use ($data, $hashedPassword, $plainPassword, $direccion, $fecha_nac, $imagen_perfil) {
            return DB::select('CALL sp_registrar_orientador(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['nombre'],
                $data['apellido'],
                $data['documento'],
                $imagen_perfil,
                $data['celular'],
                $data['genero'],
                $direccion,
                $data['id_tipo_documento'],
                $data['departamento'],
                $data['municipio'],
                $fecha_nac,
                $data['email'],
                $hashedPassword,
                $data['estado'],
            ]);
            if (!empty($results)) {
                // Retornamos tanto el resultado como la contraseña sin hashear
                return [
                    'results' => $results,
                    'plainPassword' => $plainPassword // Contraseña sin hashear
                ];
            }
        });
    }

    public function asignarAsesoriaAliado($idAsesoria, $nombreAliado)
    {
        $asesoria = Asesoria::find($idAsesoria);
        $aliado = Aliado::where('nombre', $nombreAliado)->first();

        if ($asesoria && $aliado) {
            $asesoria->id_aliado = $aliado->id;
            $asesoria->save();
            return $asesoria;
        }
        return null;
    }

    public function listarAliados()
    {
        $usuarios = User::where('estado', true)->where('id_rol', 3)->pluck('id');
        return Aliado::whereIn('id_autentication', $usuarios)->get(['nombre']);
    }

    public function contarEmprendedores()
    {
        return User::where('id_rol', 5)->where('estado', true)->count();
    }

    public function mostrarOrientadores($status)
    {
        return Orientador::select('orientador.id', 'orientador.nombre', 'orientador.apellido', 'orientador.celular', 'orientador.id_autentication', 'users.email', 'users.estado')
            ->join('users', 'orientador.id_autentication', '=', 'users.id')
            ->where('users.estado', $status)
            ->get();
    }

    public function editarOrientador($id, array $data)
    {
        $orientador = Orientador::find($id);
        if ($orientador) {
            // Eliminar el campo 'email' de los datos de actualización para no intentar actualizarlo en la tabla 'orientador'
            unset($data['email']);

            // Actualizar los demás datos del orientador
            $orientador->fill($data);

            // actualización del estado del orientador
            if (isset($data['estado'])) {
                $orientador->auth->estado = $data['estado']; // Actualiza el estado del usuario
                $orientador->auth->save(); // Guarda los cambios en el modelo de usuario asociado
            }

            // Actualizar la imagen de perfil si existe
            if (isset($data['imagen_perfil'])) {
                Storage::delete(str_replace('storage', 'public', $orientador->imagen_perfil));
                $orientador->imagen_perfil = Storage::url($data['imagen_perfil']->store('public/fotoPerfil'));
            }

            $orientador->save();

            // Si también se debe actualizar el email, se hace en la tabla 'users'
            if (isset($data['email'])) {
                $user = User::find($orientador->id_autentication);
                if ($user) {
                    $existingUser = User::where('email', $data['email'])->first();
                    if ($existingUser) {
                        throw new Exception('El correo electrónico ya ha sido registrado anteriormente');
                    }
                    $user->email = $data['email'];
                    $user->save();
                }
            }

            return $orientador;
        }
        return null;
    }


    public function obtenerPerfil($id)
    {
        return Orientador::where('orientador.id', $id)
            ->join('municipios', 'orientador.id_municipio', '=', 'municipios.id')
            ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
            ->join('users', 'orientador.id_autentication', '=', 'users.id')
            ->select('orientador.*', 'municipios.nombre as municipio_nombre', 'departamentos.name as departamento_nombre', 'departamentos.id as id_departamento', 'users.email', 'users.estado')
            ->first();
    }
}
