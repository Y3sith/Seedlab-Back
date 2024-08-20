<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use App\Models\Asesor;
use App\Models\Asesoria;
use App\Models\Emprendedor;
use App\Models\Empresa;
use App\Models\Rol;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardSuperAdminController extends Controller
{
    public function enumerarUsuarios()
    {
        $roles = Rol::all();
        $result = [];

        $totalUsers = User::count();

        foreach ($roles as $rol) {
            $countActive = User::where('id_rol', $rol->id)->where('estado', true)->count();
            $countInactive = User::where('id_rol', $rol->id)->where('estado', false)->count();
            $percentageActive = $totalUsers > 0 ? ($countActive / $totalUsers) * 100 : 0;

            $result[$rol->nombre] = [
                'activos' => $countActive,
                'inactivos' => $countInactive,
                'Porcentaje del total' => round($percentageActive, 2) . '%'
            ];
        }

        $activeUsersCount = User::where('estado', true)->count();
        $inactiveUsersCount = User::where('estado', false)->count();

        $activePercentage = $totalUsers > 0 ? ($activeUsersCount / $totalUsers) * 100 : 0;
        $inactivePercentage = $totalUsers > 0 ? ($inactiveUsersCount / $totalUsers) * 100 : 0;

        $result['activos'] = round($activePercentage, 2) . '%';
        $result['inactivos'] = round($inactivePercentage, 2) . '%';


        $top = $this->topAliados();

        $result['topAliados'] = $top;


        $result['conteoAsesorias'] = $this->asesoriasAsignadasSinAsignar();



        return response()->json($result);
    }

    public function averageAsesorias2024(Request $request)
    {
        $year = $request->input('year', 2024); // Por defecto, 2024 si no se proporciona año

        $averageAsesoriasByMonth = DB::table('asesoria')
            ->select(
                DB::raw('MONTH(fecha) as mes'),
                DB::raw('COUNT(*) / COUNT(DISTINCT doc_emprendedor) as promedio_asesorias')
            )
            ->whereRaw('YEAR(fecha) = ?', [$year])
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->orderBy(DB::raw('MONTH(fecha)'))
            ->get();

        $averageTotal = Asesoria::whereRaw('YEAR(fecha) = ?', [$year])
            ->join(
                DB::raw('(SELECT doc_emprendedor, COUNT(*) as asesoria_count FROM asesoria WHERE YEAR(fecha) = ? GROUP BY doc_emprendedor) as asesoria_counts'),
                'asesoria_counts.doc_emprendedor',
                '=',
                'asesoria.doc_emprendedor'
            )
            ->selectRaw('AVG(asesoria_counts.asesoria_count) as average_asesorias')
            ->setBindings([$year, $year]) // Asignar el año dos veces para la subconsulta
            ->value('average_asesorias');

        return [
            'promedio_mensual' => $averageAsesoriasByMonth,
            'promedio_anual' => $averageTotal
        ];
    }

    public function topAliados()
    {

        $totalAsesorias = Asesoria::count();

        $topAliados = Aliado::withCount('asesoria')
            ->orderByDesc('asesoria_count')
            ->take(5)
            ->get(['nombre', 'asesoria_count']);

        $topAliados->transform(function ($aliado) use ($totalAsesorias) {
            $porcentaje = ($aliado->asesoria_count / $totalAsesorias) * 100;
            $aliado->porcentaje = round($porcentaje, 2) . '%';
            return [
                'nombre' => $aliado->nombre,
                'asesorias' => $aliado->asesoria_count,
                'porcentaje' => $aliado->porcentaje,
            ];
        });
        return $topAliados;
    }

    public function asesoriasAsignadasSinAsignar()
    {
        $asesoriasAsignadas = Asesoria::where('asignacion', 1)->count();
        $asesoriasSinAsignar = Asesoria::where('asignacion', 0)->count();
        return [
            'asesoriasAsignadas' => $asesoriasAsignadas,
            'asesoriasSinAsignar' => $asesoriasSinAsignar
        ];
    }

    public function conteoRegistrosAnioYMes()
    {

        // $averageMonthly = DB::table('users')
        //     ->select(DB::raw('YEAR(fecha_registro) as year, MONTH(fecha_registro) as month, COUNT(*) as total'))
        //     ->groupBy(DB::raw('YEAR(fecha_registro), MONTH(fecha_registro)'))
        //     ->get();

        // $monthlyAverage = $averageMonthly->avg('total');

        // $averageYearly = DB::table('users')
        //     ->select(DB::raw('YEAR(fecha_registro) as year, COUNT(*) as total'))
        //     ->groupBy(DB::raw('YEAR(fecha_registro)'))
        //     ->get();

        // $yearlyAverage = $averageYearly->avg('total');

        // $roleIdEmprendedor = 5; // Rol emprendedor
        // $roleIdAliado = 3; // Rol aliado

        // Promedio mensual para emprendedores
        $averageMonthlyEmprendedor = DB::table('users')
            ->select(
                DB::raw("MONTH(fecha_registro) as mes"),
                DB::raw("SUM(CASE WHEN id_rol = 5 THEN 1 ELSE 0 END) as emprendedores"),
                DB::raw("SUM(CASE WHEN id_rol = 3 THEN 1 ELSE 0 END) as aliados")
            )
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get();


        // // Promedio mensual para aliados
        // $averageMonthlyAliado = DB::table('users')
        //     ->select(DB::raw('YEAR(fecha_registro) as year, MONTH(fecha_registro) as month, COUNT(*) as total'))
        //     ->where('id_rol', $roleIdAliado)
        //     ->groupBy(DB::raw('YEAR(fecha_registro), MONTH(fecha_registro)'))
        //     ->get();

        // $monthlyAverageAliado = $averageMonthlyAliado->avg('total');


        return response()->json([
            // 'monthly_average' => $monthlyAverage,
            // 'yearly_average' => $yearlyAverage,
            'promedios' => $averageMonthlyEmprendedor,
            // 'aliado' => $monthlyAverageAliado,
        ]);
    }

    public function promEmpresasXmes(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            ///me trae las empresas creadar por mes
            $anio = $request->input('fecha', date('Y'));
            $empresasPorMes = Empresa::whereYear('fecha_registro', $anio)
                ->select(DB::raw('MONTH(fecha_registro) as mes, COUNT(*) as total_empresas'))
                ->groupBy('mes')
                ->get();
            $totalMeses = $empresasPorMes->count();
            $totalEmpresas = $empresasPorMes->sum('total_empresas');
            $promedioEmpresasPorMes = $totalMeses > 0 ? $totalEmpresas / $totalMeses : 0;

            return response()->json([
                'promedioEmpresasPorMes' => round($promedioEmpresasPorMes, 2),
                'detalles' => $empresasPorMes
            ], 200);


            // // Obtener el total de empresas registradas por cada mes del año

            // $anio = $request->input('anio', date('Y'));
            // $empresasPorMes = Empresa::whereYear('fecha_registro', $anio)
            //     ->select(DB::raw('MONTH(fecha_registro) as mes, COUNT(*) as total_empresas'))
            //     ->groupBy('mes')
            //     ->get()
            //     ->keyBy('mes');
            // // Asegurarse de que se consideren todos los meses, incluso si no hubo registros
            // $meses = range(1, 12);
            // $empresasPorMesCompleto = collect($meses)->map(function ($mes) use ($empresasPorMes) {
            //     return [
            //         'mes' => $mes,
            //         'total_empresas' => $empresasPorMes->has($mes) ? $empresasPorMes[$mes]->total_empresas : 0
            //     ];
            // });
            // // Calcular el promedio de empresas registradas por mes
            // $totalEmpresas = $empresasPorMesCompleto->sum('total_empresas');
            // $promedioEmpresasPorMes = $totalEmpresas / 12;  // Considerando todos los meses del año
            // return response()->json([
            //     'promedioEmpresasPorMes' => round($promedioEmpresasPorMes, 2),
            //     'detalles' => $empresasPorMesCompleto
            // ], 200);


        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
        }
    }

    public function emprendedorXdepartamento()
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función'], 404);
            }

            // Ajusta la consulta utilizando 'documento' en lugar de 'id'
            $emprendedoresPorDepartamento = Emprendedor::join('municipios', 'emprendedor.id_municipio', '=', 'municipios.id')
                ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
                ->select('departamentos.name as departamento', DB::raw('COUNT(emprendedor.documento) as total_emprendedores'))
                ->groupBy('departamentos.id', 'departamentos.name')
                ->get();

            return response()->json($emprendedoresPorDepartamento);
        } catch (Exception $e) {
            return response()->json(['error' => ['Ocurrió un error al procesar la solicitud: ' => $e->getMessage()]], 401);
        }
    }


    //Aliado
    public function dashboardAliado($idAliado)
    {
        //CONTAR ASESORIASxALIADO SEGUN SU ESTADO (PENDIENTES O FINALIZADAS)
        $finalizadas = Asesoria::where('id_aliado', $idAliado)->whereHas('horarios', function ($query) {
            $query->where('estado', 'Finalizada');
        })->count();

        $pendientes = Asesoria::where('id_aliado', $idAliado)->whereHas('horarios', function ($query) {
            $query->where('estado', 'Pendiente');
        })->count();

        $asignadas = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 1)
            ->count();


        $sinAsignar = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 0)
            ->count();


        //CONTAR # DE ASESORES DE ESE ALIADO
        $numAsesores = Asesor::where('id_aliado', $idAliado)->count();

        $totalAsesorias = $finalizadas + $pendientes;

        // Calcular los porcentajes
        // Calcular los porcentajes
        $porcentajeFinalizadas = $totalAsesorias > 0 ? round(($finalizadas / $totalAsesorias) * 100, 2) . '%' : 0;
        $porcentajePendientes = $totalAsesorias > 0 ? round(($pendientes / $totalAsesorias) * 100, 2) . '%' : 0;

        return response()->json([
            'Asesorias Pendientes' => $pendientes,
            'Porcentaje Pendientes' => $porcentajePendientes,
            'Asesorias Finalizadas' => $finalizadas,
            'Porcentaje Finalizadas' => $porcentajeFinalizadas,
            'Asesorias Asignadas' => $asignadas,
            'Asesorias Sin Asignar' => $sinAsignar,
            'Mis Asesores' => $numAsesores,
        ]);
    }

    public function generos() //contador de cuantos usuarios son mujer/hombres u otros
    {
        try {

            if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2) {
                return response()->json(['message', 'No tienes permiso para acceder a esta funcion'], 400);
            }
            $generos = DB::table('emprendedor')
                ->select('genero', DB::raw('count(*) as total'))
                ->whereIn('genero', ['Masculino', 'Femenino', 'Otro'])
                ->groupBy('genero')
                ->get();

            $masculino = 0;
            $femenino = 0;
            $otro = 0;

            foreach ($generos as $genero) {
                if ($genero->genero == 'Femenino') {
                    $femenino = $genero->total;
                } else {
                    $masculino = $genero->total;
                    $otro = $genero->total;
                }
            }

            return response()->json([
                ['genero' => 'Femenino', 'total' => $femenino],
                ['genero' => 'Masculino', 'total' => $masculino],
                ['genero' => 'Otro', 'total' => $otro],
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    public function asesorisaTotalesAliado(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            $anio = $request->input('fecha', date('Y'));

            $asesoriasporaliado = Asesoria::whereYear('fecha', $anio)
                ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
                ->select('aliado.nombre', DB::raw('COUNT(asesoria.id) as total_asesorias'))
                ->groupBy('aliado.id', 'aliado.nombre')
                ->get();
            return response()->json($asesoriasporaliado, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
        }
    }
}
