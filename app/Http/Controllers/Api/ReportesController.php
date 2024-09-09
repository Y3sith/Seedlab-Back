<?php

namespace App\Http\Controllers\Api;

use App\Exports\AliadosExport;
use App\Exports\AsesoresAliadosExport;
use App\Exports\AsesoriasAliadosExport;
use App\Exports\AsesoriasExport;
use App\Exports\AsesoriasOrientadorExport;
use App\Exports\EmpresasExport;
use App\Exports\FormularioExport;
use App\Exports\RolesExport;
use App\Exports\SeccionExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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

    public function exportarReportesAliados(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $id_aliado = $request->input('id_aliado');

        switch ($tipo_reporte) {
            case 'asesoria':
                return Excel::download(new AsesoriasAliadosExport($id_aliado, $tipo_reporte, $fechaInicio, $fechaFin), 'asesoras_aliados.xlsx');
            case 'asesor':
                return Excel::download(new AsesoresAliadosExport($id_aliado, $tipo_reporte, $fechaInicio, $fechaFin), 'asesor_aliados.xlsx');
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }
    }

    public function exportarReporte(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');


        switch ($tipo_reporte) {
            case 'aliado':
                return Excel::download(new AliadosExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_roles.xlsx');
            case 'emprendedor':
                return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_emprendedor.xlsx');
            case 'orientador':
                return Excel::download(new RolesExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_orientadores.xlsx');
            case 'empresa':
                return Excel::download(new EmpresasExport($tipo_reporte, $fechaInicio, $fechaFin), 'empresas_registradas.xlsx');
            case 'asesoria':
                return Excel::download(new AsesoriasExport($tipo_reporte, $fechaInicio, $fechaFin), 'asesorias_solicitadas.xlsx');
            case 'asesorias_orientador':
                return Excel::download(new AsesoriasOrientadorExport($tipo_reporte, $fechaInicio, $fechaFin), 'reporte_asesorias_orientador.xlsx');
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }
    }




    public function obtenerDatosReporte(Request $request)
    {
        $tipo_reporte = $request->input('tipo_reporte');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $id_aliado = $request->input('id_aliado');

        $data = [];

        switch ($tipo_reporte) {
            case 'aliado':
                $data = DB::table('users')
                    ->join('aliado', 'users.id', '=', 'aliado.id_autentication')
                    ->select('users.id', 'aliado.nombre', 'users.email', 'users.fecha_registro', 'users.estado',  'aliado.descripcion')
                    ->whereBetween('users.fecha_registro', [$fechaInicio, $fechaFin])
                    ->get();
                break;
            case 'emprendedor':
                $data = DB::table('users')
                    ->join('emprendedor', 'users.id', '=', 'emprendedor.id_autentication')
                    ->select(
                        'users.id',
                        'users.email',
                        'users.fecha_registro',
                        'users.estado',
                        'emprendedor.documento',
                        'emprendedor.nombre',
                        'emprendedor.apellido',
                        'emprendedor.celular',
                        'emprendedor.genero',
                        'emprendedor.fecha_nac',
                        'emprendedor.direccion',
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
            case 'asesorias_aliados':

                break;
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        return response()->json($data);
    }

    public function mostrarReportesAliados(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $id_aliado = $request->input('id_aliado');
        $tipo_reporte = $request->input('tipo_reporte');

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
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }


        return response()->json($data);
    }

    public function procesarRespuestas($idEmprendedor)
    {
        $respuestas = DB::table('respuesta')
            ->join('empresa', 'respuesta.id_empresa', '=', 'empresa.documento')
            ->where('empresa.id_emprendedor', $idEmprendedor)
            ->select('respuesta.respuestas_json')
            ->get();

        // Obtener todos los id_pregunta y id_subpregunta únicos del JSON
        $idsPreguntas = [];
        $idsSubpreguntas = [];
        foreach ($respuestas as $respuesta) {
            $respuestas_array = json_decode($respuesta->respuestas_json, true);
            foreach ($respuestas_array as $respuesta_json) {
                if (isset($respuesta_json['id_pregunta'])) {
                    $idsPreguntas[] = $respuesta_json['id_pregunta'];
                }
                if (isset($respuesta_json['id_subpregunta'])) {
                    $idsSubpreguntas[] = $respuesta_json['id_subpregunta'];
                }
            }
        }
        $idsPreguntas = array_unique($idsPreguntas);
        $idsSubpreguntas = array_unique($idsSubpreguntas);

        // Obtener los nombres de las preguntas, secciones y subpreguntas para los ids únicos
        $preguntas = DB::table('pregunta')
            ->whereIn('id', $idsPreguntas)
            ->pluck('nombre', 'id');

        $secciones = DB::table('pregunta')
            ->join('seccion', 'pregunta.id_seccion', '=', 'seccion.id')
            ->whereIn('pregunta.id', $idsPreguntas)
            ->pluck('seccion.nombre', 'pregunta.id_seccion');  // Obtenemos las secciones

        $subpreguntas = DB::table('subpregunta')
            ->whereIn('id', $idsSubpreguntas)
            ->pluck('texto', 'id');

        // Array para almacenar los resultados procesados
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
                        'subpregunta' => $subpreguntas[$idSubpregunta] ?? 'Subpregunta desconocida',
                        'respuesta_texto' => $respuesta_json['texto_res'] ?? null, // Añadir el texto_res
                    ];
                }
            } else {
                Log::error('JSON inválido o no decodificable: ' . $respuesta->respuestas_json);
            }
        }
        //dd($resultados);
        // Crear la exportación
        $export = new SeccionExport($resultados);

        // Devolver el archivo Excel
        return Excel::download($export, 'Reporte_Formulario.xlsx');
    }
}
