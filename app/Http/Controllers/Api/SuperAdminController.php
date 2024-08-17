<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Emprendedor;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\PersonalizacionSistema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;
use App\Models\Asesoria;
use App\Models\Aliado;
use App\Models\Asesor;
use Exception;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function personalizacionSis(Request $request, $id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }
        // Buscar la personalización existente
        $personalizacion = PersonalizacionSistema::where('id', $id)->first();
        if (!$personalizacion) {
            return response()->json([
                'message' => 'Personalización no encontrada'
            ], 404);
        }

        // Actualizar otros campos
        $personalizacion->nombre_sistema = $request->input('nombre_sistema');
        $personalizacion->color_principal = $request->input('color_principal');
        $personalizacion->color_secundario = $request->input('color_secundario');
        //$personalizacion->color_terciario = $request->input('color_terciario');
        $personalizacion->id_superadmin = $request->input('id_superadmin');
        $personalizacion->descripcion_footer = $request->input('descripcion_footer');
        $personalizacion->paginaWeb = $request->input('paginaWeb');
        $personalizacion->email = $request->input('email');
        $personalizacion->telefono = $request->input('telefono');
        $personalizacion->direccion = $request->input('direccion');
        $personalizacion->ubicacion = $request->input('ubicacion');

        // Manejo de archivos
        if ($request->hasFile('logo_footer') && $request->file('logo_footer')->isValid()) {
            $logoFooterPath = $request->file('logo_footer')->store('public/logos');
            $personalizacion->logo_footer = Storage::url($logoFooterPath);
        }

        if ($request->hasFile('imagen_logo') && $request->file('imagen_logo')->isValid()) {
            $imagenLogoPath = $request->file('imagen_logo')->store('public/logos');
            $personalizacion->imagen_logo = Storage::url($imagenLogoPath);
        }

        $personalizacion->save();

        return response()->json(['message' => 'Personalización del sistema actualizada correctamente'], 200);
    }



    public function obtenerPersonalizacion()
    {
        $personalizaciones = PersonalizacionSistema::first();
        //dd($personalizaciones);

        if (!$personalizaciones) {
            return response()->json([
                'message' => 'No se encontraron personalizaciones del sistema'
            ], 404);
        }
        // $imageBase64 = $personalizaciones->imagen_logo;
        // if (strpos($imageBase64, 'data:image/png;base64,') === false) {
        //     // Si no contiene el prefijo, agregarlo
        //     $imageBase64 = 'data:image/png;base64,' . $imageBase64;
        // }
        return response()->json([
            'imagen_logo' => $personalizaciones->imagen_logo ? $this->correctImageUrl($personalizaciones->imagen_logo) : null,
            'nombre_sistema' => $personalizaciones->nombre_sistema,
            'color_principal' => $personalizaciones->color_principal,
            'color_secundario' => $personalizaciones->color_secundario,
            //'color_terciario' => $personalizaciones->color_terciario,
            //'logo_footer' => $personalizaciones->logo_footer ? $this->correctImageUrl($personalizaciones->logo_footer) : null,
            'descripcion_footer' => $personalizaciones->descripcion_footer,
            'paginaWeb' => $personalizaciones->paginaWeb,
            'email' => $personalizaciones->email,
            'telefono' => $personalizaciones->telefono,
            'direccion' => $personalizaciones->direccion,
            'ubicacion' => $personalizaciones->ubicacion,
        ], 200);
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function crearSuperAdmin(Request $data)
    {


        try {
            $response = null;
            $statusCode = 200;

            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }

            $perfilUrl = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $logoPath = $data->file('imagen_perfil')->store('public/fotoPerfil');
                $perfilUrl = Storage::url($logoPath);
            }


            DB::transaction(function () use ($data, &$response, &$statusCode, $perfilUrl) {
                $results = DB::select('CALL sp_registrar_superadmin(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $perfilUrl,
                    $data['direccion'],
                    $data['celular'],
                    $data['genero'],
                    $data['email'],
                    Hash::make($data['password']),
                    $data['estado'],
                ]);

                if (!empty($results)) {
                    $response = $results[0]->mensaje;
                    if ($response === 'El correo electrónico ya ha sido registrado anteriormente' || $response === 'El numero de celular ya ha sido registrado en el sistema') {
                        $statusCode = 400;
                    }
                }
            });
            return response()->json(['message' => $response], $statusCode);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function userProfileAdmin($id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            $admin = SuperAdmin::where('id', $id)
                ->with('auth:id,email,estado')
                ->select('id', 'nombre', 'apellido', "id_autentication")
                ->first();
            return [
                'id' => $admin->id,
                'nombre' => $admin->nombre,
                'apellido' => $admin->apellido,
                'email' => $admin->auth->email,
                'estado' => $admin->auth->estado == 1 ? 'Activo' : 'Inactivo',
                'id_auth' => $admin->id_autentication
            ];
            //return response()->json($admin);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function mostrarSuperAdmins(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 401);
            }

            $estado = $request->input('estado', 'Activo'); // Obtener el estado desde el request, por defecto 'Activo'

            $estadoBool = $estado === 'Activo' ? 1 : 0;

            $adminVer = User::where('estado', $estadoBool)
                ->where('id_rol', 1)
                ->pluck('id');

            $admins = SuperAdmin::whereIn('id_autentication', $adminVer)
                ->with('auth:id,email,estado')
                ->get(['id', 'nombre', 'apellido', 'id_autentication']);

            $adminsConEstado = $admins->map(function ($admin) {
                $user = User::find($admin->id_autentication);

                return [
                    'id' => $admin->id,
                    'nombre' => $admin->nombre,
                    'apellido' => $admin->apellido,
                    'id_auth' => $user->id,
                    'email' => $user->email,
                    'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo'

                ];
            });

            return response()->json($adminsConEstado);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function editarSuperAdmin(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            $admin = SuperAdmin::find($id);
            if ($admin) {
                $admin->nombre = $request->input('nombre');
                $admin->apellido = $request->input('apellido');
                $admin->save();

                if ($admin->auth) {
                    $user = $admin->auth;

                    $password = $request->input('password');
                    if ($password) {
                        $user->password =  Hash::make($request->input('password'));
                    }

                    $newEmail = $request->input('email');
                    if ($newEmail && $newEmail !== $user->email) {
                        // Verificar si el nuevo email ya está en uso
                        $existingUser = User::where('email', $newEmail)->first();
                        if ($existingUser) {
                            return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
                        }
                        $user->email = $newEmail;
                    }
                    // $user->email = $request->input('email');
                    // //  if ($user->email) {
                    // //      return response()->json(['message'=>'El correo electrónico ya ha sido registrado anteriormente'],501);
                    // //  }
                    // //dd($user->email);
                    $user->estado = $request->input('estado');
                    $user->save();
                }
                return response()->json(['message' => 'Superadministrador actualizado correctamente'], 200);
            } else {
                return response()->json(['message' => 'Superadministrador no encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: '], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     if(Auth::user()->id_rol !=1){
    //         return response()->json([
    //            'message' => 'No tienes permiso para acceder a esta ruta'
    //         ], 401);
    //     }

    //     $superAdmin = SuperAdmin::find($id);
    //     if(!$superAdmin){
    //         return response()->json([
    //            'message' => 'SuperAdmin no encontrado'
    //         ], 404);
    //     }

    //     $user = $superAdmin->auth;
    //     $user->estado = 0;
    //     $user->save();

    //     return response()->json(['message' =>'SuperAdmin desactivado'], 200);

    // }

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


    public function restore($id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json([
                    'message' => 'No tienes permiso para acceder a esta ruta'
                ], 401);
            }
            // Buscar la personalización por su ID
            $personalizacion = PersonalizacionSistema::find($id);

            if (!$personalizacion) {
                return response()->json([
                    'message' => 'Personalización no encontrada'
                ], 404);
            }

            // Restaurar los valores originales (puedes definir los valores originales manualmente o tenerlos guardados previamente)
            $personalizacion->nombre_sistema = 'SeedLab';
            $personalizacion->color_principal = '#00B3ED';
            $personalizacion->color_secundario = '#FA7D00';
            $personalizacion->descripcion_footer = 'Este programa estará enfocado en emprendimientos de base tecnológica, para ideas validadas, que cuenten con un codesarrollo, prototipado y pruebas de concepto. Se va a abordar en temas como Big Data, ciberseguridad e IA, herramientas de hardware y software, inteligencia competitiva, vigilancia tecnológica y propiedad intelectual.';
            $personalizacion->paginaWeb = 'seedlab.com';
            $personalizacion->email = 'email@seedlab.com';
            $personalizacion->telefono = '(55) 5555-5555';
            $personalizacion->direccion = 'Calle 48 # 28 - 40';
            $personalizacion->ubicacion = 'Bucaramanga, Santander, Colombia';
            $personalizacion->imagen_logo = '/storage/logos/5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.jpg';

            // Guardar los cambios
            $personalizacion->save();

            return response()->json([
                'message' => 'Personalización restaurada correctamente',
                $personalizacion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar la personalización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listarAliados()
    {

        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para esta funcion'], 400);
            }
            $aliados = Aliado::whereHas('auth', function ($query) {
                $query->where('estado', '1');
            })->get(['id', 'nombre']);
            return response()->json($aliados, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
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
                return response()->json(['message' => 'no tienes permisos para acceder a esta funcion'], 404);
            }
            $emprendedoresPorMunicipio = Emprendedor::with('municipios')
                ->select('id_municipio', DB::raw('COUNT(*) as total_emprendedores'))
                ->groupBy('id_municipio')
                ->get()
                ->map(function ($emprendedor) {
                    return [
                        'municipio' => $emprendedor->municipios->nombre,
                        'total_emprendedores' => $emprendedor->total_emprendedores,
                    ];
                });
            return response()->json($emprendedoresPorMunicipio);
        } catch (Exception $e) {
            return response()->json(['error' => ['Ocurrió un error al procesar la solicitud: ' => $e->getMessage()], 401]);
        }
    }

    // public function emprendedoresPorMunicipioPDF (){
    //     $emprendedoresPorMunicipio = Emprendedor::with('municipios')
    //     ->select('id_municipio', DB::raw('COUNT(*) as total_emprendedores'))
    //     ->groupBy('id_municipio')
    //     ->get()
    //     ->map(function($emprendedor) {
    //         return [
    //             'municipio' => $emprendedor->municipios->nombre, 
    //             'total_emprendedores' => $emprendedor->total_emprendedores,
    //         ];
    //     });

    //     $pdf = PDF::loadView('emprendedores_municipio_pdf', ['emprendedores' => $emprendedoresPorMunicipio]); ///->cambiar la vista que genera el pdf
    //     return $pdf->download('reporte_emprendedores_municipio.pdf');
    // }


}
