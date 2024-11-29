<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmpresaService;
use Illuminate\Support\Facades\Log;

class EmpresaApiController extends Controller
{
    protected $empresaService;

    public function __construct(EmpresaService $empresaService)
    {
        $this->empresaService = $empresaService;
    }

    //Función para traer todas las empresas en la grafica araña dashboards
    public function index()
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $empresas = $this->empresaService->obtenerEmpresas();
        return response()->json($empresas);
    }

    /**
     * Muestra una lista empresas por emprendedor
     */
    public function obtenerEmpresasPorEmprendedor(Request $request)
    {
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $empresas = $this->empresaService->obtenerEmpresasPorEmprendedor($request->input('doc_emprendedor'));
        return response()->json($empresas);
    }

    //Funcion para traer los datos a la vista
    public function getOnlyEmpresa($id_emprendedor, $documento)
    {
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $empresa = $this->empresaService->obtenerEmpresaPorIdYDocumento($id_emprendedor, $documento);
        if (!$empresa) {
            return response()->json(["error" => "Empresa no encontrada"], 404);
        }

        return response()->json($empresa);
    }

    //Función para crear solo empresa
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            // Verificar permisos de usuario
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
            }

            // Validar la estructura del request
            $request->validate([
                'empresa.nombre' => 'required|string|max:50',
                'empresa.documento' => 'required|string|max:50',
                'empresa.cargo' => 'required|string|max:50',
                'empresa.razonSocial' => 'required|string|max:50',
                'empresa.url_pagina' => 'nullable|string',
                'empresa.telefono' => 'nullable|string|max:20',
                'empresa.celular' => 'required|string|max:20',
                'empresa.direccion' => 'required|string|max:50',
                'empresa.correo' => 'required|email|max:100',
                'empresa.profesion' => 'required|string|max:100',
                'empresa.experiencia' => 'required|string|max:300',
                'empresa.funciones' => 'required|string|max:300',
                'empresa.id_tipo_documento' => 'required|integer',
                'empresa.id_departamento' => 'required|integer',
                'empresa.id_municipio' => 'required|integer',
            ]);

            // Llamar al servicio para crear la empresa y los apoyos
            $response = $this->empresaService->crearEmpresa($data);

            return response()->json([
                'message' => $response['message'],
                'empresa' => $response['empresa'],
                'apoyos' => $response['apoyos'],
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Esto detecta el error de duplicado
                return response()->json(['message' => 'Ya existe una empresa con este número de documento'], 409);
            }
    
            // Cualquier otro error
            return response()->json(['message' => 'Ocurrió un error al crear la empresa'], 500);
        }
    }


    //Función para actualizar solo empresa
    public function update(Request $request, $documento)
    {
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
        }

        $empresa = $this->empresaService->actualizarEmpresa($documento, $request->except('apoyo'));
        if (!$empresa) {
            return response()->json(["error" => "Empresa no encontrada"], 404);
        }

        return response()->json(["message" => "Empresa actualizada"], 200);
    }
}
