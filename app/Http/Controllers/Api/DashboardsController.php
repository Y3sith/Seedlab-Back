<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Services\DashboardService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardsController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }

    public function averageAsesorias(Request $request)
    {
        $year = $request->input('year', date('Y'));

        try {
            $result = $this->dashboardService->getAverageAsesorias($year);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al calcular el promedio de asesorías: ' . $e->getMessage()], 500);
        }
    }

    public function topAliados()
    {
        try {
            $topAliados = $this->dashboardService->getTopAliados();
            return response()->json($topAliados, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el top de aliados: ' . $e->getMessage()], 500);
        }
    }

    public function asesoriasAsignadasSinAsignar()
    {
        try {
            $result = $this->dashboardService->getAsesoriasAsignadasSinAsignar();
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías asignadas y sin asignar: ' . $e->getMessage()], 500);
        }
    }

    public function conteoRegistrosAnioYMes()
    {
        try {
            $averageMonthlyEmprendedor = $this->dashboardService->getConteoRegistrosAnioYMes();
            return response()->json([
                'promedios' => $averageMonthlyEmprendedor,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el conteo de registros: ' . $e->getMessage()], 500);
        }
    }

    public function emprendedorXdepartamento()
    {
        try {
            $emprendedoresPorDepartamento = $this->dashboardService->getEmprendedoresPorDepartamento();
            return response()->json($emprendedoresPorDepartamento, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener emprendedores por departamento: ' . $e->getMessage()], 500);
        }
    }

    public function dashboardAliado($idAliado)
    {
        try {
            $data = $this->dashboardService->getDashboardAliado($idAliado);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el dashboard del aliado: ' . $e->getMessage()], 500);
        }
    }

    public function generos()
    {
        try {
            $result = $this->dashboardService->getGeneros();
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener los géneros: ' . $e->getMessage()], 500);
        }
    }

    public function asesoriasTotalesAliado(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para esta función'], 403);
            }

            $anio = $request->input('fecha', date('Y'));
            $asesoriasPorAliado = $this->dashboardService->getAsesoriasTotalesAliado($anio);

            return response()->json($asesoriasPorAliado, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías totales por aliado: ' . $e->getMessage()], 500);
        }
    }

    public function asesoriasXmes($id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función.'], 403);
            }

            $asesorias = $this->dashboardService->getAsesoriasPorMes($id);
            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías por mes: ' . $e->getMessage()], 500);
        }
    }

    public function getRadarChartData($id_empresa, $tipo)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función.'], 403);
            }

            $puntajes = $this->dashboardService->getRadarChartData($id_empresa, $tipo);

            if (!$puntajes) {
                return response()->json(['message' => 'No se encontró puntaje para esta empresa'], 404);
            }

            return response()->json($puntajes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos del radar: ' . $e->getMessage()], 500);
        }
    }

    public function getDashboardData(Request $request)
    {
        try {
            // 1. Contar usuarios por rol y estado
            $usersByRoleAndState = $this->dashboardService->getUsersByRoleAndState();

            $roles = Rol::all();
            $result = [];

            foreach ($roles as $rol) {
                $activeUsers = $usersByRoleAndState->where('id_rol', $rol->id)->where('estado', true)->first();
                $inactiveUsers = $usersByRoleAndState->where('id_rol', $rol->id)->where('estado', false)->first();

                $result['usuarios'][$rol->nombre] = [
                    'activos' => $activeUsers ? $activeUsers->total : 0,
                    'inactivos' => $inactiveUsers ? $inactiveUsers->total : 0,
                ];
            }

            // 2. Top aliados
            $result['topAliados'] = $this->dashboardService->getTopAliados();

            // 3. Conteo de asesorías
            $result['conteoAsesorias'] = $this->dashboardService->getAsesoriasAsignadasSinAsignar();

            // 4. Promedio de asesorías por año
            $year = $request->input('year', date('Y'));
            $result['averageAsesorias'] = $this->dashboardService->getAverageAsesorias($year);

            // 5. Conteo de registros por año y mes
            $result['conteoRegistros'] = $this->dashboardService->getConteoRegistrosAnioYMes();

            // 6. Emprendedores por departamento
            $result['emprendedoresPorDepartamento'] = $this->dashboardService->getEmprendedoresPorDepartamento();

            // 7. Géneros Emprendedores
            $result['generosEmprendedores'] = $this->dashboardService->getGeneros();

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos del dashboard: ' . $e->getMessage()], 500);
        }
    }
}
