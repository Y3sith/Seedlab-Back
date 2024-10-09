<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use App\Models\Asesor;
use App\Models\Asesoria;
use App\Models\Emprendedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService extends Controller
{
    public function getAverageAsesorias($year)
    {
        // Cálculo mensual
        $averageAsesoriasByMonth = DB::table('asesoria')
            ->select(
                DB::raw('MONTH(fecha) as mes'),
                DB::raw('COUNT(*) / COUNT(DISTINCT doc_emprendedor) as promedio_asesorias')
            )
            ->whereYear('fecha', $year)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->orderBy(DB::raw('MONTH(fecha)'))
            ->get();

        // Cálculo anual
        $averageTotal = DB::table('asesoria')
            ->selectRaw('AVG(asesoria_count) as average_asesorias')
            ->from(DB::raw('(SELECT doc_emprendedor, COUNT(*) as asesoria_count FROM asesoria WHERE YEAR(fecha) = ? GROUP BY doc_emprendedor) as asesoria_counts'))
            ->setBindings([$year])
            ->value('average_asesorias');
            
        // Preparar el resultado final
        return [
            'promedio_mensual' => $averageAsesoriasByMonth,
            'promedio_anual' => $averageTotal
        ];
    }

    public function getTopAliados()
    {
        $topAliados = Aliado::select('aliado.id', 'aliado.nombre')
            ->selectRaw('COUNT(asesoria.id) as asesorias')
            ->leftJoin('asesoria', 'aliado.id', '=', 'asesoria.id_aliado')
            ->groupBy('aliado.id', 'aliado.nombre')
            ->having('asesorias', '>', 0)
            ->orderByDesc('asesorias')
            ->get();

        return $topAliados;
    }

    public function getAsesoriasAsignadasSinAsignar()
    {
        $result = DB::table('asesoria')
            ->select(
                DB::raw('SUM(CASE WHEN asignacion = 1 THEN 1 ELSE 0 END) as asesoriasAsignadas'),
                DB::raw('SUM(CASE WHEN asignacion = 0 THEN 1 ELSE 0 END) as asesoriasSinAsignar')
            )
            ->first();

        return $result;
    }

    public function getConteoRegistrosAnioYMes()
    {
        $averageMonthlyEmprendedor = DB::table('users')
            ->select(
                DB::raw("MONTH(fecha_registro) as mes"),
                DB::raw("COUNT(CASE WHEN id_rol = 5 THEN 1 END) as emprendedores"),
                DB::raw("COUNT(CASE WHEN id_rol = 3 THEN 1 END) as aliados")
            )
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get();

        return $averageMonthlyEmprendedor;
    }

    public function getEmprendedoresPorDepartamento()
    {
        $emprendedoresPorDepartamento = Emprendedor::leftJoin('municipios', 'emprendedor.id_municipio', '=', 'municipios.id')
            ->leftJoin('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
            ->select('departamentos.name as departamento', DB::raw('COUNT(emprendedor.documento) as total_emprendedores'))
            ->groupBy('departamentos.id', 'departamentos.name')
            ->get();

        return $emprendedoresPorDepartamento;
    }

    public function getGeneros()
    {
        $generos = DB::table('emprendedor')
            ->select('genero', DB::raw('count(*) as total'))
            ->whereIn('genero', ['Masculino', 'Femenino', 'Otro'])
            ->groupBy('genero')
            ->get();

        return $generos;
    }

    public function getDashboardAliado($idAliado)
    {
        // Asesorías finalizadas
        $finalizadas = Asesoria::where('id_aliado', $idAliado)
            ->whereHas('horarios', function ($query) {
                $query->where('estado', 'Finalizada');
            })
            ->count();

        // Asesorías pendientes
        $pendientes = Asesoria::where('id_aliado', $idAliado)
            ->whereHas('horarios', function ($query) {
                $query->where('estado', 'Pendiente');
            })
            ->count();

        // Asesorías asignadas
        $asignadas = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 1)
            ->count();

        // Asesorías sin asignar
        $sinAsignar = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 0)
            ->count();

        // Asesores activos
        $numAsesoresActivos = Asesor::where('id_aliado', $idAliado)
            ->whereHas('auth', function ($query) {
                $query->where('estado', 1);
            })
            ->count();

        // Asesores inactivos
        $numAsesoresInactivos = Asesor::where('id_aliado', $idAliado)
            ->whereHas('auth', function ($query) {
                $query->where('estado', 0);
            })
            ->count();

        $totalAsesorias = $finalizadas + $pendientes;

        // Calcular porcentajes
        $porcentajeFinalizadas = $totalAsesorias > 0 ? round(($finalizadas / $totalAsesorias) * 100, 2) . '%' : '0%';
        $porcentajePendientes = $totalAsesorias > 0 ? round(($pendientes / $totalAsesorias) * 100, 2) . '%' : '0%';

        return [
            'Asesorias Pendientes' => $pendientes,
            'Porcentaje Pendientes' => $porcentajePendientes,
            'Asesorias Finalizadas' => $finalizadas,
            'Porcentaje Finalizadas' => $porcentajeFinalizadas,
            'Asesorias Asignadas' => $asignadas,
            'Asesorias Sin Asignar' => $sinAsignar,
            'Asesores Activos' => $numAsesoresActivos,
            'Asesores Inactivos' => $numAsesoresInactivos,
        ];
    }

    public function getRadarChartData($id_empresa, $tipo)
    {
        $campo = ($tipo == 1) ? 'primera_vez' : 'segunda_vez';

        $puntajes = DB::table('puntaje')
            ->where('puntaje.documento_empresa', $id_empresa)
            ->where($campo, 1)
            ->select(
                'info_general',
                'info_financiera',
                'info_mercado',
                'info_trl',
                'info_tecnica'
            )
            ->first();

        if (!$puntajes) {
            return null;
        }

        return [
            'info_general' => $puntajes->info_general,
            'info_financiera' => $puntajes->info_financiera,
            'info_mercado' => $puntajes->info_mercado,
            'info_trl' => $puntajes->info_trl,
            'info_tecnica' => $puntajes->info_tecnica
        ];
    }

    public function getAsesoriasTotalesAliado($anio)
    {
        $asesoriasPorAliado = Asesoria::whereYear('fecha', $anio)
            ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
            ->select('aliado.nombre', DB::raw('COUNT(asesoria.id) as total_asesorias'))
            ->groupBy('aliado.id', 'aliado.nombre')
            ->get();

        return $asesoriasPorAliado;
    }

    public function getAsesoriasPorMes($id)
    {
        $ano = date('Y');

        $asesorias = Asesoria::where('id_aliado', $id)
            ->whereYear('fecha', $ano)
            ->selectRaw('MONTH(fecha) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->get();

        return $asesorias;
    }

    public function getUsersByRoleAndState()
    {
        $usersByRoleAndState = User::selectRaw('id_rol, estado, COUNT(*) as total')
            ->groupBy('id_rol', 'estado')
            ->get();

        return $usersByRoleAndState;
    }
}
