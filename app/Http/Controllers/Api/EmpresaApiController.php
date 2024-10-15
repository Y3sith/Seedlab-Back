<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmpresaService;

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
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
        }

        $empresaData = $request->input('empresa');
        $empresa = $this->empresaService->crearEmpresa($empresaData);

        return response()->json(['message' => 'Empresa creada exitosamente', 'empresa' => $empresa], 201);
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
