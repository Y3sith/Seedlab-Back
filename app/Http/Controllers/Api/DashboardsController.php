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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class DashboardsController extends Controller
{
    public function enumerarUsuarios()
    {
        $cacheKey = 'dashboard:enumerarUsuarios';

        // Verifica si los datos ya están en Redis
        $cachedData = Redis::get($cacheKey);
        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        // Una única consulta para contar usuarios por rol y estado
        $usersByRoleAndState = User::selectRaw('id_rol, estado, COUNT(*) as total')
            ->groupBy('id_rol', 'estado')
            ->get();

        $roles = Rol::all();
        $totalUsers = User::count();

        $result = [];

        foreach ($roles as $rol) {
            // Filtrar los usuarios por rol y estado
            $activeUsers = $usersByRoleAndState->where('id_rol', $rol->id)->where('estado', true)->first();
            $inactiveUsers = $usersByRoleAndState->where('id_rol', $rol->id)->where('estado', false)->first();

            $countActive = $activeUsers ? $activeUsers->total : 0;
            $countInactive = $inactiveUsers ? $inactiveUsers->total : 0;

            $result[$rol->nombre] = [
                'activos' => $countActive,
                'inactivos' => $countInactive,
            ];
        }

        // Incluir top aliados y asesorías
        $result['topAliados'] = $this->topAliados();
        $result['conteoAsesorias'] = $this->asesoriasAsignadasSinAsignar();

        // Cachear los datos en Redis para futuras solicitudes
        Redis::set($cacheKey, json_encode($result));
        Redis::expire($cacheKey, 3600);

        return response()->json($result);
    }


    public function averageAsesorias2024(Request $request)
    {
        $cacheKey = 'dashboard:averageAsesorias2024';
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        $year = $request->input('year', 2024); // Por defecto, 2024 si no se proporciona año

        // cálculo mensual
        $averageAsesoriasByMonth = DB::table('asesoria')
            ->select(
                DB::raw('MONTH(fecha) as mes'),
                DB::raw('COUNT(*) / COUNT(DISTINCT doc_emprendedor) as promedio_asesorias')
            )
            ->whereYear('fecha', $year)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->orderBy(DB::raw('MONTH(fecha)'))
            ->get();

        //  cálculo anual
        $averageTotal = DB::table('asesoria')
            ->selectRaw('AVG(asesoria_count) as average_asesorias')
            ->from(DB::raw('(SELECT doc_emprendedor, COUNT(*) as asesoria_count FROM asesoria WHERE YEAR(fecha) = ? GROUP BY doc_emprendedor) as asesoria_counts'))
            ->setBindings([$year])
            ->value('average_asesorias');

        // Preparar el resultado final
        $result = [
            'promedio_mensual' => $averageAsesoriasByMonth,
            'promedio_anual' => $averageTotal
        ];

        // Guardar el resultado en caché por 1 hora
        Redis::set($cacheKey, json_encode($result));
        Redis::expire($cacheKey, 3600); // 1 hora

        return response()->json($result, 200);
    }


    public function topAliados()
    {
        $cacheKey = 'dashboard:topAliados';
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        // Consulta optimizada para obtener los top 5 aliados por número de asesorías
        $topAliados = Aliado::select('aliado.id', 'aliado.nombre') // Asegura que ambos campos están en el GROUP BY
            ->selectRaw('COUNT(asesoria.id) as asesoria')
            ->leftJoin('asesoria', 'aliado.id', '=', 'asesoria.id_aliado')
            ->groupBy('aliado.id', 'aliado.nombre')  // Agrupa por ambas columnas
            ->orderByDesc('asesoria')
            ->take(5)
            ->get();

        // Cachear el resultado en Redis por 1 hora
        Redis::set($cacheKey, json_encode($topAliados));
        Redis::expire($cacheKey, 3600); // 1 hora

        return response()->json($topAliados, 200);
    }



    public function asesoriasAsignadasSinAsignar()
    {
        $cacheKey = 'dashboard:asesoriasAsignadasSinAsignar';
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        // Una sola consulta para obtener ambos conteos
        $result = DB::table('asesoria')
            ->select(
                DB::raw('SUM(CASE WHEN asignacion = 1 THEN 1 ELSE 0 END) as asesoriasAsignadas'),
                DB::raw('SUM(CASE WHEN asignacion = 0 THEN 1 ELSE 0 END) as asesoriasSinAsignar')
            )
            ->first();

        // Almacenar el resultado en Redis
        Redis::set($cacheKey, json_encode($result));
        Redis::expire($cacheKey, 3600); // 1 hora de caché

        return response()->json($result, 200);
    }


    public function conteoRegistrosAnioYMes()
    {
        $cacheKey = 'dashboard:conteoRegistroAnioMes';
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        $averageMonthlyEmprendedor = DB::table('users')
            ->select(
                DB::raw("MONTH(fecha_registro) as mes"),
                DB::raw("COUNT(CASE WHEN id_rol = 5 THEN 1 END) as emprendedores"),
                DB::raw("COUNT(CASE WHEN id_rol = 3 THEN 1 END) as aliados")
            )
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get();

        Redis::set($cacheKey, json_encode($averageMonthlyEmprendedor));
        Redis::expire($cacheKey, 3600); // Cache por 1 hora

        return response()->json([
            'promedios' => $averageMonthlyEmprendedor,
        ]);
    }


    public function emprendedorXdepartamento()
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta función'], 404);
            }

            $cacheKey = 'dashboard:emprendedorXdepartamento';
            $cachedData = Redis::get($cacheKey);

            if ($cachedData) {
                return response()->json(json_decode($cachedData), 200);
            }

            // Ajusta la consulta utilizando 'documento' en lugar de 'id'
            $emprendedoresPorDepartamento = Emprendedor::join('municipios', 'emprendedor.id_municipio', '=', 'municipios.id')
                ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
                ->select('departamentos.name as departamento', DB::raw('COUNT(emprendedor.documento) as total_emprendedores'))
                ->groupBy('departamentos.id', 'departamentos.name')
                ->get();

            if (!$cachedData) {
                Redis::set($cacheKey, json_encode($emprendedoresPorDepartamento));
                Redis::expire($cacheKey, 3600);
            }

            return response()->json($emprendedoresPorDepartamento);
        } catch (Exception $e) {
            return response()->json(['error' => ['Ocurrió un error al procesar la solicitud: ' => $e->getMessage()]], 401);
        }
    }


    //Aliado
    public function dashboardAliado($idAliado)
    {
        $cacheKey = 'dashboard:aliado' . $idAliado;
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }
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
        $porcentajeFinalizadas = $totalAsesorias > 0 ? round(($finalizadas / $totalAsesorias) * 100, 2) . '%' : 0;
        $porcentajePendientes = $totalAsesorias > 0 ? round(($pendientes / $totalAsesorias) * 100, 2) . '%' : 0;

        if (!$cachedData) {
            Redis::set($cacheKey, json_encode([
                'Asesorias Pendientes' => $pendientes,
                'Porcentaje Pendientes' => $porcentajePendientes,
                'Asesorias Finalizadas' => $finalizadas,
                'Porcentaje Finalizadas' => $porcentajeFinalizadas,
                'Asesorias Asignadas' => $asignadas,
                'Asesorias Sin Asignar' => $sinAsignar,
                'Mis Asesores' => $numAsesores,
            ]));
            Redis::expire($cacheKey, 3600);
        }

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
            $cacheKey = 'dashboar:datosGeneros';

            $cachedData = Redis::get($cacheKey);

            if ($cachedData) {
                return response()->json(json_decode($cachedData), 200);
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

            $result = [
                ['genero' => 'Femenino', 'total' => $femenino],
                ['genero' => 'Masculino', 'total' => $masculino],
                ['genero' => 'Otro', 'total' => $otro],
            ];

            // Almacena los datos en Redis para futuras solicitudes
            if (!$cachedData) {
                Redis::set($cacheKey, json_encode($result));
                Redis::expire($cacheKey, 3600); // Expiración de 1 hora (opcional)
            }

            // Devuelve la respuesta con los datos calculados
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function asesoriasTotalesAliado(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            $cacheKey = 'dashboard:asesoriasTotalesAliado';
            $cachedData = Redis::get($cacheKey);

            if ($cachedData) {
                return response()->json(json_decode($cachedData), 200);
            }
            $anio = $request->input('fecha', date('Y'));

            $asesoriasporaliado = Asesoria::whereYear('fecha', $anio)
                ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
                ->select('aliado.nombre', DB::raw('COUNT(asesoria.id) as total_asesorias'))
                ->groupBy('aliado.id', 'aliado.nombre')
                ->get();

            if (!$cachedData) {
                Redis::set($cacheKey, json_encode($asesoriasporaliado));
                Redis::expire($cacheKey, 3600);
            }

            return response()->json($asesoriasporaliado, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
        }
    }

    public function asesoriasXmes($id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para acceder a esta funciona.']);
            }
            $cacheKey = 'dashboard:asesoriasXmes' . $id;
            $cachedData = Redis::get($cacheKey);

            if ($cachedData) {
                return response()->json(json_decode($cachedData), 200);
            }

            $ano = date('Y');
            $asesorias = Asesoria::where('id_aliado', $id)
                ->whereYear('fecha', $ano)
                ->selectRaw('MONTH(fecha) as mes, COUNT(*) as total') //selecciona el mes y luego cuenta las asesorias
                ->groupBy('mes')
                ->get();

            if (!$cachedData) {
                Redis::set($cacheKey, json_encode($asesorias));
                Redis::expire($cacheKey, 3600);
            }

            return response()->json($asesorias);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getRadarChartData($id_empresa, $tipo)
    {
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2 && Auth::user()->id_rol != 5) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta función.'], 403);
        }

        // Determinar el campo a consultar basado en el tipo
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
            return response()->json(['message' => 'No se encontró puntaje para esta empresa'], 404);
        }

        // array asociativo
        $puntajeArray = [
            'info_general' => $puntajes->info_general,
            'info_financiera' => $puntajes->info_financiera,
            'info_mercado' => $puntajes->info_mercado,
            'info_trl' => $puntajes->info_trl,
            'info_tecnica' => $puntajes->info_tecnica
        ];


        return response()->json($puntajeArray, 200);
    }


    public function getDashboardData(Request $request)
    {
        $cacheKey = 'dashboard:allData';
        $cachedData = Redis::get($cacheKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData), 200);
        }

        // Obtén todos los datos necesarios

        // 1. Contar usuarios por rol y estado
        $usersByRoleAndState = User::selectRaw('id_rol, estado, COUNT(*) as total')
            ->groupBy('id_rol', 'estado')
            ->get();

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
        $result['topAliados'] = $this->topAliados();

        // 3. Conteo de asesorías
        $result['conteoAsesorias'] = $this->asesoriasAsignadasSinAsignar();

        // 4. Promedio de asesorías por año
        $result['averageAsesorias'] = $this->averageAsesorias2024($request);

        // 5. Conteo de registros por año y mes
        $result['conteoRegistros'] = $this->conteoRegistrosAnioYMes();

        // 6. Emprendedores por departamento
        $result['emprendedoresPorDepartamento'] = $this->emprendedorXdepartamento();

        //7. Generos Emprendedores
        $result['generosEmprendedores'] = $this->generos();

        // Almacena el resultado en Redis
        Redis::set($cacheKey, json_encode($result));
        Redis::expire($cacheKey, 3600); // 1 hora de caché

        return response()->json($result, 200);
    }

}
