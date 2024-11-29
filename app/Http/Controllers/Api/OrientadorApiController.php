<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrientadorService;
use Illuminate\Http\Request;

class OrientadorApiController extends Controller
{
    protected $orientadorService;

    // Constructor que inicializa el servicio del orientador
    public function __construct(OrientadorService $orientadorService)
    {
        $this->orientadorService = $orientadorService;
    }

    /**
     * Crea un nuevo orientador.
     */
    public function createOrientador(Request $request)
    {
        // Recibe los datos de la solicitud
        $data = $request->all();
        // Llama al servicio para crear el orientador
        $response = $this->orientadorService->crearOrientador($data);

        // Verifica si se ha producido un error y ajusta el código de estado
        if ($response['status'] === 200) {
            return response()->json([
                'message' => $response['mensaje'],
                'email' => $response['email']
            ], 200);
        } else {
            return response()->json([
                'message' => $response['mensaje']
            ], $response['status']);
        }
    }

    /**
     * Asigna una asesoría a un aliado.
     */
    public function asignarAsesoriaAliado(Request $request, $idAsesoria)
    {
        // Obtiene el nombre del aliado de la solicitud
        $nombreAliado = $request->input('nombreAliado');
        // Llama al servicio para asignar la asesoría al aliado
        $this->orientadorService->asignarAsesoriaAliado($idAsesoria, $nombreAliado);
        return response()->json(['message' => 'Aliado asignado correctamente'], 200);
    }

    /**
     * Lista todos los aliados.
     */
    public function listarAliados()
    {
         // Llama al servicio para obtener la lista de aliados
        $aliados = $this->orientadorService->listarAliados();
        return response()->json($aliados, 200);
    }

    //Cuenta el número de emprendedores activos.
    public function contarEmprendedores()
    {
        // Llama al servicio para contar los emprendedores activos
        $count = $this->orientadorService->contarEmprendedores();
        return response()->json(['Emprendedores activos' => $count], 200);
    }

    //Muestra una lista de orientadores con un estado específico.
    public function mostrarOrientadores($status)
    {
        // Llama al servicio para mostrar los orientadores filtrados por estado
        $orientadores = $this->orientadorService->mostrarOrientadores($status);
        return response()->json($orientadores, 200);
    }

    //Edita un orientador específico.
    public function editarOrientador(Request $request, $id)
    {
        // Recibe los datos de la solicitud
        $data = $request->all();

        // Llama al servicio para editar los datos del orientador
        $orientador = $this->orientadorService->editarOrientador($id, $data);
        return response()->json(['message' => 'Orientador actualizado correctamente', 'data' => $orientador], 200);
    }

    //Muestra el perfil de un orientador específico.
    public function userProfileOrientador($id)
    {
        // Llama al servicio para obtener el perfil del orientador
        $orientador = $this->orientadorService->obtenerPerfil($id);
        return response()->json($orientador, 200);
    }
}
