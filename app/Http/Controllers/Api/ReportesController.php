<?php

namespace App\Http\Controllers\Api;

use App\Exports\AsesoriasExport;
use App\Exports\EmpresasExport;
use App\Exports\RolesExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{
    public function exportarExcelRoles(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte.xlsx');
    }

    public function exportarEmpresasRegistradas(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new EmpresasExport($tipo_reporte, $fechaInicio, $fechaFin), 'empresas_registradas.xlsx');
    }

    public function exportarAsesorias(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new AsesoriasExport($tipo_reporte, $fechaInicio, $fechaFin), 'asesorias_solicitadas.xlsx');
    }

    public function exportarReporte(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        switch ($tipo_reporte) {
            case 'aliado':
                return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_roles.xlsx');
            case 'emprendedor':
                return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_emprendedor.xlsx');
            case 'orientador':
                return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_orientadores.xlsx');
            case 'empresa':
                return Excel::download(new EmpresasExport($tipo_reporte, $fechaInicio, $fechaFin), 'empresas_registradas.xlsx');
            case 'asesoria':
                return Excel::download(new AsesoriasExport($tipo_reporte, $fechaInicio, $fechaFin), 'asesorias_solicitadas.xlsx');
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }
    }

    public function obtenerDatosReporte(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $data = [];

        switch ($tipo_reporte) {
            case 'aliado':
                $data = DB::table('users')
                    ->join('aliado', 'users.id', '=', 'aliado.id_autentication')
                    ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', 'aliado.nombre', 'aliado.descripcion')
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'emprendedor':
                $data = DB::table('users')
                    ->join('emprendedor', 'users.id', '=', 'emprendedor.id_autentication')
                    ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', 'emprendedor.documento',
                    'emprendedor.nombre','emprendedor.apellido','emprendedor.celular','emprendedor.genero','emprendedor.fecha_nac','emprendedor.direccion',)
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'orientador':
                $data = DB::table('users')
                    ->join('orientador', 'users.id', '=', 'orientador.id_autentication')
                    ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', 'orientador.nombre', 'orientador.apellido',
                    'orientador.documento', 'orientador.celular', 'orientador.genero', 'orientador.fecha_nac', 'orientador.direccion')
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'empresa':
                $data = DB::table('empresa')
                    ->join('emprendedor', 'empresa.id_emprendedor', '=', 'emprendedor.documento')
                    ->select('empresa.documento','empresa.razonSocial','empresa.url_pagina','empresa.telefono', 'empresa.celular', 
                    'empresa.direccion','empresa.correo','empresa.fecha_registro','emprendedor.nombre', 'emprendedor.apellido', 'emprendedor.documento')
                    ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'asesoria':
                $data = DB::table('asesoria')
                    ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
                    ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
                    ->select('asesoria.Nombre_sol', 'asesoria.notas','asesoria.fecha','aliado.nombre as nombre_aliado', 'emprendedor.nombre as emprendedor')
                    ->whereBetween('asesoria.fecha', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        return response()->json($data);
    }
}
