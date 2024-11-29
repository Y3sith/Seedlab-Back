<?php

namespace App\Services;

use App\Jobs\EnviarNotificacionCrearUsuario;
use App\Repositories\Orientador\OrientadorRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OrientadorService
{

    protected $orientadorRepository;

    public function __construct(OrientadorRepositoryInterface $orientadorRepository)
    {
        $this->orientadorRepository = $orientadorRepository;
    }

    public function crearOrientador(array $data)
    {
        // Generar una contraseña aleatoria y crear su hash
        $data['random_password'] = bin2hex(random_bytes(4));
        $data['hashed_password'] = Hash::make($data['random_password']);

        // Llamar al método crearOrientador del repositorio
        $results = $this->orientadorRepository->crearOrientador($data);

        if (!empty($results)) {
            $result = $results[0]->mensaje;
            $plainPassword = $data['random_password'];  // Contraseña sin hashear

            // Verificamos si el correo ya ha sido registrado
            if ($result === 'El correo electrónico ya ha sido registrado anteriormente' || $result === 'El numero de celular ya ha sido registrado en el sistema') {
                return ['mensaje' => $result, 'status' => 400];
            } else {
                $email = $results[0]->email;

                // Si se registra el orientador correctamente, enviamos el correo con la contraseña sin hashear
                if ($email) {
                    Log::info('Despachando job para enviar notificación', ['email' => $email]);
                    EnviarNotificacionCrearUsuario::dispatch($email, 'Orientador', $plainPassword);
                }

                // Devuelve el mensaje de éxito con el email
                return [
                    'mensaje' => 'El orientador ha sido creado con éxito',
                    'email' => $email,   // Devolver también el email
                    'status' => 200
                ];
            }
        }

        return ['mensaje' => 'Error al crear orientador', 'status' => 500];
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
        $orientadores = $this->orientadorRepository->mostrarOrientadores($status);

        // Mapear los datos
        $orientadoresConEstado = $orientadores->map(function ($orientador) {
            return [
                'id' => $orientador->id,
                'nombre' => $orientador->nombre,
                'apellido' => $orientador->apellido,
                'celular' => $orientador->celular,
                'estado' => $orientador->estado == 1 ? 'Activo' : 'Inactivo',
                'email' => $orientador->email,
                'id_auth' => $orientador->id_autentication,
            ];
        });

        return $orientadoresConEstado;
    }


    public function editarOrientador($id, array $data)
    {
        return $this->orientadorRepository->editarOrientador($id, $data);
    }

    public function obtenerPerfil($id)
    {
        $orientador = $this->orientadorRepository->obtenerPerfil($id);

        if (!$orientador) {
            throw new Exception('Orientador no encontrado', 404);
        }

        return [
            'id' => $orientador->id,
            'nombre' => $orientador->nombre,
            'apellido' => $orientador->apellido,
            'documento' => $orientador->documento,
            'id_tipo_documento' => $orientador->id_tipo_documento,
            'fecha_nac' => $orientador->fecha_nac,
            'imagen_perfil' => $orientador->imagen_perfil,
            'direccion' => $orientador->direccion,
            'celular' => $orientador->celular,
            'genero' => $orientador->genero,
            'id_municipio' => $orientador->id_municipio,
            'id_departamento' => $orientador->id_departamento,
            'municipio_nombre' => $orientador->municipio_nombre,
            'departamento_nombre' => $orientador->departamento_nombre,
            'email' => $orientador->email,
            'estado' => $orientador->estado == 1 ? 'Activo' : 'Inactivo', // Transformar el estado
        ];
    }
}
