<?php

namespace App\Http\Controllers\Api;

use App\Exports\AliadosExport;
use App\Exports\AsesoresAliadosExport;
use App\Exports\AsesoriasAliadosExport;
use App\Exports\AsesoriasExport;
use App\Exports\AsesoriasOrientadorExport;
use App\Exports\EmpresasExport;
use App\Exports\RolesExport;
use App\Exports\SeccionExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{



    public function exportarReportesAliados(Request $request)
    {
        // Recupera los parámetros del request.
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $id_aliado = $request->input('id_aliado');
        $formato = $request->input('formato', 'excel');

        // Formatea la fecha de inicio si se proporciona.
        if ($fechaInicio) {
            $fechaInicio = date('Y-m-d H:i:s', strtotime($fechaInicio . ' 00:00:00'));
        }

        // Selecciona el tipo de reporte y crea la instancia de exportación correspondiente.
        switch ($tipo_reporte) {
            case 'asesoria':
                $export = new AsesoriasAliadosExport($id_aliado, $tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'asesorias_aliados';
                $plantilla = 'reporte_asesorias_aliado_template';
                break;
            case 'asesor':
                $export = new AsesoresAliadosExport($id_aliado, $tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'asesores_aliados';
                $plantilla = 'reporte_asesores_aliado_template';
                break;
            default:
                // Retorna un error si el tipo de reporte no es válido.
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        // Exporta el reporte en formato Excel.
        if ($formato === 'excel') {
            return Excel::download($export, "{$nombreArchivo}.xlsx");
        }
        try {
            // obtenemos los datos a exportar
            $datos = json_decode(json_encode($export->collection()), true);
            $pdf = Pdf::loadView($plantilla, compact('datos'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("{$nombreArchivo}.pdf");
        } catch (\Exception $e) {
            // Maneja el error al generar el PDF, registrando el error en los logs.
            Log::error('Error al generar el PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar el reporte PDF'], 500);
        }
    }

    //Exporta un reporte según el tipo especificado en formato Excel o PDF.
    public function exportarReporte(Request $request)
    {
        // Recupera los parámetros del request.
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $formato = $request->input('formato', 'excel');

        // Formatea la fecha de fin si se proporciona.
        if ($fechaFin) {
            $fechaFin = date('Y-m-d H:i:s', strtotime($fechaFin . ' 23:59:59'));
        }

        // Formatea la fecha de inicio si se proporciona.
        if ($fechaInicio) {
            $fechaInicio = date('Y-m-d H:i:s', strtotime($fechaInicio . ' 00:00:00'));
        }

        // Selecciona el tipo de reporte y crea la instancia de exportación correspondiente.
        switch ($tipo_reporte) {
            case 'aliado':
                $export = new AliadosExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'reporte_roles';
                $plantilla = 'reporte_aliados_template';
                break;
            case 'emprendedor':
                $export = new RolesExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'reporte_emprendedor';
                $plantilla = 'reporte_pdf_template';
                break;
            case 'orientador':
                $export = new RolesExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'reporte_orientadores';
                $plantilla = 'reporte_pdf_template';
                break;
            case 'empresa':
                $export = new EmpresasExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'empresas_registradas';
                $plantilla = 'reporte_empresas_template';
                break;
            case 'asesoria':
                $export = new AsesoriasExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'asesorias_solicitadas';
                $plantilla = 'reporte_asesorias_template';
                break;
            case 'asesorias_orientador':
                $export = new AsesoriasOrientadorExport($tipo_reporte, $fechaInicio, $fechaFin);
                $nombreArchivo = 'reporte_asesorias_orientador';
                break;
            default:
                // Retorna un error si el tipo de reporte no es válido.
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        // Si el formato es Excel, se descarga el archivo correspondiente.
        if ($formato === 'excel') {
            return Excel::download($export, "{$nombreArchivo}.xlsx");
        }

        // Si el formato es PDF, se genera y descarga el archivo PDF.
        try {
            // Obtiene los datos a exportar.
            $datos = json_decode(json_encode($export->collection()), true);
            $pdf = Pdf::loadView($plantilla, compact('datos'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("{$nombreArchivo}.pdf");
        } catch (\Exception $e) {
            // Maneja el error al generar el PDF, registrando el error en los logs.
            Log::error('Error al generar el PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar el reporte PDF'], 500);
        }
    }


    //Obtiene los datos de un reporte según el tipo especificado y el rango de fechas.
    public function obtenerDatosReporte(Request $request)
    {
        // Recupera los parámetros del request.
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Agrega el tiempo a la fecha de inicio y fin.
        $fechaInicio .= ' 00:00:00';
        $fechaFin .= ' 23:59:59';

        $data = [];

        // Selecciona los datos según el tipo de reporte.
        switch ($tipo_reporte) {
            case 'aliado':
                $data = DB::table('users')
                    ->join('aliado', 'users.id', '=', 'aliado.id_autentication')
                    ->select('users.id', 'aliado.nombre', 'users.email', 'users.fecha_registro', 'users.estado', 'aliado.descripcion')
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'emprendedor':
                $data = DB::table('users')
                    ->join('emprendedor', 'users.id', '=', 'emprendedor.id_autentication')
                    ->select(
                        'users.id',
                        'users.email',
                        'emprendedor.documento',
                        'emprendedor.nombre',
                        'emprendedor.apellido',
                        'emprendedor.celular',
                        'emprendedor.direccion'
                    )
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'orientador':
                $data = DB::table('users')
                    ->join('orientador', 'users.id', '=', 'orientador.id_autentication')
                    ->select(
                        'users.id',
                        'users.email',
                        'users.fecha_registro',
                        'users.estado',
                        'orientador.nombre',
                        'orientador.apellido',
                        'orientador.documento',
                        'orientador.celular',
                        'orientador.genero',
                        'orientador.fecha_nac',
                        'orientador.direccion'
                    )
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'empresa':
                $data = DB::table('empresa')
                    ->join('emprendedor', 'empresa.id_emprendedor', '=', 'emprendedor.documento')
                    ->select(
                        'empresa.documento',
                        'empresa.razonSocial',
                        'empresa.url_pagina',
                        'empresa.telefono',
                        'empresa.celular',
                        'empresa.direccion',
                        'empresa.correo',
                        'empresa.fecha_registro',
                        'emprendedor.nombre',
                        'emprendedor.apellido',
                        'emprendedor.celular as celular_emprendedor'
                    )
                    ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'asesoria':
                $data = DB::table('asesoria')
                    ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
                    ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
                    ->select('asesoria.Nombre_sol', 'asesoria.notas', 'asesoria.fecha', 'aliado.nombre as nombre_aliado', 'emprendedor.nombre as emprendedor')
                    ->whereBetween('asesoria.fecha', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'asesorias_orientador':
                $data = DB::table('asesoria')
                    ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
                    ->select('asesoria.Nombre_sol', 'asesoria.notas', 'asesoria.fecha', 'emprendedor.nombre as nombre_emprendedor', 'emprendedor.documento')
                    ->where('isorientador', 1)
                    ->whereBetween('asesoria.fecha', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            default:
                // Retorna un error si el tipo de reporte no es válido.
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        // Devuelve los datos obtenidos en formato JSON.
        return response()->json($data);
    }

    //Muestra los reportes de aliados según el tipo de reporte y el rango de fechas especificado.
    public function mostrarReportesAliados(Request $request)
    {
        // Recupera los parámetros del request.
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $id_aliado = $request->input('id_aliado');
        $tipo_reporte = $request->input('tipo_reporte');

        // Selecciona los datos según el tipo de reporte.
        switch ($tipo_reporte) {
            case 'asesoria':
                $data = DB::table('asesoria')
                    ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
                    ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
                    ->select(
                        'asesoria.Nombre_sol',
                        'asesoria.notas',
                        'asesoria.fecha',
                        'emprendedor.nombre as nombre_emprendedor',
                        'emprendedor.documento',
                        'aliado.nombre as nombre_aliado'
                    )
                    ->where('asesoria.id_aliado', $id_aliado)
                    ->whereBetween('asesoria.fecha', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'asesor':
                $data = DB::table('asesor')
                    ->join('aliado', 'asesor.id_aliado', '=', 'aliado.id')
                    ->join('users', 'asesor.id_autentication', '=', 'users.id')
                    ->select(
                        'asesor.nombre',
                        'asesor.apellido',
                        'asesor.documento',
                        'asesor.celular',
                        'asesor.fecha_nac',
                        'asesor.direccion',
                        'users.email',
                        'users.fecha_registro',
                        DB::raw('(CASE WHEN users.estado = 1 THEN "Activo" ELSE "Inactivo" END) as estado')
                    )
                    ->where('asesor.id_aliado', $id_aliado)
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            default:
                // Retorna un error si el tipo de reporte no es válido.
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        // Devuelve los datos obtenidos en formato JSON.
        return response()->json($data);
    }


    //Procesa las respuestas de un emprendedor y genera un reporte en Excel.
    public function procesarRespuestas($idEmprendedor, $documentoEmpresa = null)
    {
        // Obtener las respuestas uniendo las tablas 'respuesta', 'empresa' y 'puntaje'.
        $query = DB::table('respuesta')
            ->join('empresa', 'respuesta.id_empresa', '=', 'empresa.documento')
            ->join('puntaje', 'empresa.documento', '=', 'puntaje.documento_empresa') // Unir con 'puntaje'
            ->where('empresa.id_emprendedor', $idEmprendedor)
            ->select(
                'respuesta.respuestas_json',
                'puntaje.info_general',
                'puntaje.info_financiera',
                'puntaje.info_mercado',
                'puntaje.info_trl',
                'puntaje.info_tecnica',
                'puntaje.primera_vez',
                'puntaje.segunda_vez'
            );

        // Filtrar por documento de empresa si se proporciona.
        if (!is_null($documentoEmpresa)) {
            $query->where('empresa.documento', $documentoEmpresa);
        }

        // Ejecutar la consulta y obtener las respuestas.
        $respuestas = $query->get();

        // Obtener todos los id_pregunta y id_subpregunta únicos del JSON.
        $idsPreguntas = [];
        $idsSubpreguntas = [];
        foreach ($respuestas as $respuesta) {
            $respuestas_array = json_decode($respuesta->respuestas_json, true);
            if (is_array($respuestas_array)) {
                foreach ($respuestas_array as $respuesta_json) {
                    if (isset($respuesta_json['id_pregunta'])) {
                        $idsPreguntas[] = $respuesta_json['id_pregunta'];
                    }
                    if (isset($respuesta_json['id_subpregunta'])) {
                        $idsSubpreguntas[] = $respuesta_json['id_subpregunta'];
                    }
                }
            } else {
                Log::error('JSON inválido o no decodificable: ' . $respuesta->respuestas_json);
            }
        }

        // Eliminar duplicados de los IDs de preguntas y subpreguntas.
        $idsPreguntas = array_unique($idsPreguntas);
        $idsSubpreguntas = array_unique($idsSubpreguntas);

        Log::info('IDs de preguntas y subpreguntas', ['preguntas' => $idsPreguntas, 'subpreguntas' => $idsSubpreguntas]);

        // Obtener los nombres de las preguntas, secciones y subpreguntas para los IDs únicos.
        $preguntas = DB::table('pregunta')
            ->whereIn('id', $idsPreguntas)
            ->pluck('nombre', 'id');

        $secciones = DB::table('pregunta')
            ->join('seccion', 'pregunta.id_seccion', '=', 'seccion.id')
            ->whereIn('pregunta.id', $idsPreguntas)
            ->pluck('seccion.nombre', 'pregunta.id_seccion'); // Obtener secciones

        $subpreguntas = DB::table('subpregunta')
            ->whereIn('id', $idsSubpreguntas)
            ->pluck('texto', 'id');

        // Array para almacenar los resultados procesados.
        $resultados = [];

        foreach ($respuestas as $respuesta) {
            $respuestas_array = json_decode($respuesta->respuestas_json, true);
            if (is_array($respuestas_array)) {
                foreach ($respuestas_array as $respuesta_json) {
                    $idPregunta = $respuesta_json['id_pregunta'] ?? null;
                    $idSubpregunta = $respuesta_json['id_subpregunta'] ?? null;
                    $idSeccion = DB::table('pregunta')->where('id', $idPregunta)->value('id_seccion'); // Trae el id_seccion asociado a la pregunta

                    $resultados[] = [
                        'seccion' => $secciones[$idSeccion] ?? 'Sección desconocida', // Añadir la sección
                        'opcion' => $respuesta_json['opcion'] ?? null,
                        'valor' => $respuesta_json['valor'] ?? null,
                        'verform_pr' => $respuesta_json['verform_pr'] ?? null,
                        'fecha_reg' => $respuesta_json['fecha_reg'] ?? null,
                        'pregunta' => $preguntas[$idPregunta] ?? 'Pregunta desconocida',
                        'subpregunta' => $subpreguntas[$idSubpregunta] ?? 'No contiene Subpregunta',
                        'respuesta_texto' => $respuesta_json['texto_res'] ?? null, // Añadir el texto_res
                        // Añadir los puntajes
                        'info_general' => $respuesta->info_general,
                        'info_financiera' => $respuesta->info_financiera,
                        'info_mercado' => $respuesta->info_mercado,
                        'info_trl' => $respuesta->info_trl,
                        'info_tecnica' => $respuesta->info_tecnica,
                        'primera_vez' => $respuesta->primera_vez,
                        'segunda_vez' => $respuesta->segunda_vez,
                    ];
                }
            } else {
                Log::error('JSON inválido o no decodificable: ' . $respuesta->respuestas_json);
            }
        }

        Log::info('Datos procesados para la exportación', ['resultados' => $resultados]);

        // Crear la exportación.
        $export = new SeccionExport($resultados);

        Log::info('Exportación creada, enviando archivo...');

        // Devolver el archivo Excel.
        return Excel::download($export, 'Reporte_Formulario.xlsx');
    }



    public function mostrarReporteFormEmprendedor(Request $request)
    {
        // Obtener parámetros de la solicitud.
        $docEmprendedor = $request->input('doc_emprendedor');
        $tipo_reporte = $request->input('tipo_reporte'); // 1 = Primera vez, 2 = Segunda vez
        $empresa = $request->input('empresa');

        // Construir la consulta utilizando joins para unir las tablas necesarias.
        $query = DB::table('respuesta AS r')
            ->join('empresa AS e', 'r.id_empresa', '=', 'e.documento')
            ->join('puntaje AS p', 'p.documento_empresa', '=', 'e.documento')
            ->select('r.verform_pr', 'r.verform_se', 'e.nombre AS nombre_empresa', 'p.*')
            ->where('e.documento', $empresa) // Filtrar por documento de la empresa
            ->where('e.id_emprendedor', $docEmprendedor); // Filtrar por ID del emprendedor

        // Filtrar según el tipo de reporte solicitado
        if ($tipo_reporte == '1') { // Primera vez
            $query->where('r.verform_pr', 1)
                ->where('r.verform_se', 0)
                ->where('p.primera_vez', 1)
                ->where('p.segunda_vez', 0);
        } elseif ($tipo_reporte == '2') { // Segunda vez
            $query->where('r.verform_pr', 0)
                ->where('r.verform_se', 1)
                ->where('p.primera_vez', 0)
                ->where('p.segunda_vez', 1);
        } else {
            return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        // Ejecutar la consulta y obtener los resultados.
        $resultados = $query->get();

        // Devolver los resultados en formato JSON.
        return response()->json($resultados);
    }
}
