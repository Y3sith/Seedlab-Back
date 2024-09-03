<?php

namespace App\Http\Controllers\Api;

use App\Exports\EmpresasExport;
use App\Exports\RolesExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{
    public function exportarExcelRoles(Request $request)
    {
        $rol = $request->input('rol');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new RolesExport($rol, $fechaInicio, $fechaFin), 'reporte.xlsx');
    }

    public function exportarEmpresasRegistradas(Request $request){
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new EmpresasExport($fechaInicio, $fechaFin), 'empresas_registradas.xlsx');
    }
}
