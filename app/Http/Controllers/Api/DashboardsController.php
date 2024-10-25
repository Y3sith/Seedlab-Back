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

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Obtiene el promedio de asesorías por año.
     * @param Request $request - Solicitud con el parámetro opcional del año.
     * @return JsonResponse - Promedio de asesorías.
     */
    public function averageAsesorias(Request $request)
    {
        $year = $request->input('year', date('Y'));

        try {
            // Llama al servicio para obtener el promedio de asesorías.
            $result = $this->dashboardService->getAverageAsesorias($year);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al calcular el promedio de asesorías: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los aliados con más interacciones.
     * @return JsonResponse - Lista de los aliados principales.
     */
    public function topAliados()
    {
        try {
            // Llama al servicio para obtener el top de aliados.
            $topAliados = $this->dashboardService->getTopAliados();
            return response()->json($topAliados, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el top de aliados: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el número de asesorías asignadas y no asignadas.
     * @return JsonResponse - Asesorías asignadas y sin asignar.
     */
    public function asesoriasAsignadasSinAsignar()
    {
        try {
            // Llama al servicio para obtener las asesorías asignadas y sin asignar.
            $result = $this->dashboardService->getAsesoriasAsignadasSinAsignar();
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías asignadas y sin asignar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el conteo de registros por año y mes.
     * @return JsonResponse - Conteo de registros mensuales.
     */
    public function conteoRegistrosAnioYMes()
    {
        try {
            // Llama al servicio para obtener el conteo de registros por año y mes.
            $averageMonthlyEmprendedor = $this->dashboardService->getConteoRegistrosAnioYMes();
            return response()->json([
                'promedios' => $averageMonthlyEmprendedor,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el conteo de registros: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el número de emprendedores por departamento.
     * @return JsonResponse - Emprendedores agrupados por departamento.
     */
    public function emprendedorXdepartamento()
    {
        try {
            // Llama al servicio para obtener los emprendedores por departamento.
            $emprendedoresPorDepartamento = $this->dashboardService->getEmprendedoresPorDepartamento();
            return response()->json($emprendedoresPorDepartamento, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener emprendedores por departamento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los datos del dashboard para un aliado específico.
     * @param int $idAliado - El ID del aliado.
     * @return JsonResponse - Datos del dashboard del aliado.
     */
    public function dashboardAliado($idAliado)
    {
        try {
            // Llama al servicio para obtener el dashboard del aliado.
            $data = $this->dashboardService->getDashboardAliado($idAliado);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el dashboard del aliado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los géneros de los emprendedores.
     * @return JsonResponse - Distribución de géneros.
     */
    public function generos()
    {
        try {
            // Llama al servicio para obtener la distribución de géneros.
            $result = $this->dashboardService->getGeneros();
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener los géneros: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el total de asesorías realizadas por aliados en un año.
     * @param Request $request - Solicitud con el parámetro opcional del año.
     * @return JsonResponse - Asesorías totales por aliado.
     */
    public function asesoriasTotalesAliado(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para esta función'], 403);
            }
            // Obtiene el año de la solicitud, o el año actual por defecto.
            $anio = $request->input('fecha', date('Y'));

            // Llama al servicio para obtener las asesorías totales por aliado.
            $asesoriasPorAliado = $this->dashboardService->getAsesoriasTotalesAliado($anio);

            return response()->json($asesoriasPorAliado, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías totales por aliado: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Obtiene el número de asesorías mensuales de un aliado.
     * @param int $id - El ID del aliado.
     * @return JsonResponse - Asesorías por mes.
     */
    public function asesoriasXmes($id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función.'], 403);
            }

            // Llama al servicio para obtener las asesorías por mes del aliado.
            $asesorias = $this->dashboardService->getAsesoriasPorMes($id);
            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener asesorías por mes: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los datos del gráfico radar para una empresa.
     * @param int $id_empresa - El ID de la empresa.
     * @param string $tipo - El tipo de radar.
     * @return JsonResponse - Datos del gráfico radar.
     */
    public function getRadarChartData($id_empresa, $tipo)
    {
        try {
            if (!in_array(Auth::user()->id_rol, [1, 2, 5])) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función.'], 403);
            }

            // Llama al servicio para obtener los datos del gráfico radar.
            $puntajes = $this->dashboardService->getRadarChartData($id_empresa, $tipo);

            if (!$puntajes) {
                return response()->json(['message' => 'No se encontró puntaje para esta empresa'], 404);
            }

            return response()->json($puntajes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener datos del radar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene múltiples datos del dashboard, como usuarios, asesorías, aliados, registros, etc.
     * @param Request $request - Solicitud con parámetros opcionales como el año.
     * @return JsonResponse - Datos del dashboard.
     */
    public function getDashboardData(Request $request)
    {
        try {
            // 1. Contar usuarios por rol y estado
            $usersByRoleAndState = $this->dashboardService->getUsersByRoleAndState();

            $roles = Rol::all();
            $result = [];

            // Procesa los datos de usuarios activos e inactivos.
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
