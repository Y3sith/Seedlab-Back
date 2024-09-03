<?php

namespace App\Http\Controllers\Api;

use App\Exports\AsesoriasExport;
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
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new EmpresasExport($tipo_reporte,$fechaInicio, $fechaFin), 'empresas_registradas.xlsx');
    }

    public function exportarAsesorias(Request $request){
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        return Excel::download(new AsesoriasExport($tipo_reporte,$fechaInicio, $fechaFin), 'asesorias_solicitadas.xlsx');
    }

    public function obtenerReportesDisponibles()
    {
        // Suponiendo que tienes una lista fija de reportes disponibles
        // Puedes ajustar esto según cómo generes los reportes
        $reportes = [
            ['nombre' => 'Aliados', 'url' => '/api/reporte_roles?tipo_reporte=aliado'],
            ['nombre' => 'Emprendedores', 'url' => '/api/reporte_roles?tipo_reporte=emprendedor'],
            ['nombre' => 'Orientadores', 'url' => '/api/reporte_roles?tipo_reporte=orientador'],
            ['nombre' => 'Empresas', 'url' => '/api/reporte_empresas?tipo_reporte=empresa'],
            ['nombre' => 'Asesorias Solicitadas', 'url' => '/api/reporte_roles?tipo_reporte=asesoria']
        ];

        return response()->json($reportes);
    }
}
