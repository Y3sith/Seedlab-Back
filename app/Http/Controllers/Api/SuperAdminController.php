<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Models\PersonalizacionSistema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Aliado;
use Exception;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redis;

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
            // Genera la URL completa correctamente
            $personalizacion->logo_footer = asset('storage/logos/' . basename($logoFooterPath));
        }

        if ($request->hasFile('imagen_logo') && $request->file('imagen_logo')->isValid()) {
            $imagenLogoPath = $request->file('imagen_logo')->store('public/logos');
            // Genera la URL completa correctamente
            $personalizacion->imagen_logo = asset('storage/logos/' . basename($imagenLogoPath));
        }



        $personalizacion->save();

        // Almacenar la personalización en Redis para futuras consultas
        $personalizacionKey = 'personalizacion:' . $id;
        Redis::set($personalizacionKey, json_encode($personalizacion)); // Guarda como JSON
        Redis::expire($personalizacionKey, 3600); // Opcional, expira en 1 hora

        return response()->json(['message' => 'Personalización del sistema actualizada correctamente'], 200);
    }



    public function obtenerPersonalizacion($id)
    {
        // Recuperar datos del localStorage en el front-end
        $personalizacion = json_decode(request()->header('localStorage'), true);

        // Verificar si la personalización ya está almacenada en localStorage
        if (isset($personalizacion[$id])) {
            return response()->json($personalizacion[$id], 200);
        }

        // Obtener la personalización desde la base de datos
        $personalizacion = PersonalizacionSistema::where('id', $id)->first();

        if (!$personalizacion) {
            return response()->json([
                'message' => 'No se encontraron personalizaciones del sistema'
            ], 404);
        }

        // Preparar la respuesta
        $personalizacionParaCache = [
            'imagen_logo' => $personalizacion->imagen_logo ? $this->correctImageUrl($personalizacion->imagen_logo) : null,
            'nombre_sistema' => $personalizacion->nombre_sistema,
            'color_principal' => $personalizacion->color_principal,
            'color_secundario' => $personalizacion->color_secundario,
            'descripcion_footer' => $personalizacion->descripcion_footer,
            'paginaWeb' => $personalizacion->paginaWeb,
            'email' => $personalizacion->email,
            'telefono' => $personalizacion->telefono,
            'direccion' => $personalizacion->direccion,
            'ubicacion' => $personalizacion->ubicacion,
        ];

        
        
        return response()->json($personalizacionParaCache, 200);
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

            $direccion = $data->input('direccion', 'Dirección por defecto');
            $fecha_nac = $data->input('fecha_nac', '2000-01-01');

            $imagen_perfil = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
                $imagen_perfil = Storage::url($imagenPath);
            }

            DB::transaction(function () use ($data, &$response, &$statusCode, $direccion, $fecha_nac, $imagen_perfil) {
                $results = DB::select('CALL sp_registrar_superadmin(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $data['documento'],
                    $imagen_perfil,
                    $data['celular'],
                    $data['genero'],
                    $direccion,
                    $data['id_tipo_documento'],
                    $data['departamento'],
                    $data['municipio'],
                    $fecha_nac,
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
            $admin = SuperAdmin::where('superadmin.id', $id)
                ->join('municipios', 'superadmin.id_municipio', '=', 'municipios.id')
                ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
                ->select(
                    'superadmin.id',
                    'superadmin.nombre',
                    'superadmin.apellido',
                    'superadmin.documento',
                    'superadmin.id_tipo_documento',
                    'superadmin.fecha_nac',
                    'superadmin.imagen_perfil',
                    'superadmin.direccion',
                    'superadmin.celular',
                    'superadmin.genero',
                    'superadmin.id_municipio',
                    'municipios.nombre as municipio_nombre',
                    'departamentos.name as departamento_nombre',
                    'departamentos.id as id_departamento',
                    'superadmin.id_autentication'
                )
                ->first();

            return [
                'id' => $admin->id,
                'nombre' => $admin->nombre,
                'apellido' => $admin->apellido,
                'documento' => $admin->documento,
                'id_tipo_documento' => $admin->id_tipo_documento,
                'fecha_nac' => $admin->fecha_nac,
                'imagen_perfil' => $admin->imagen_perfil ? $this->correctImageUrl($admin->imagen_perfil) : null,
                'direccion' => $admin->direccion,
                'celular' => $admin->celular,
                'genero' => $admin->genero,
                'id_departamento' => $admin->id_departamento,
                'id_municipio' => $admin->id_municipio,
                'email' => $admin->auth->email,
                'estado' => $admin->auth->estado == 1 ? 'Activo' : 'Inactivo',
                'id_auth' => $admin->id_autentication
            ];
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

            return response()->json($adminsConEstado, 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
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

            $requiredFields = [
                'nombre',
                'apellido',
                'documento',
                'celular',
                'genero',
                'direccion',
                'id_tipo_documento',
                'id_departamento',
                'id_municipio',
                'fecha_nac',
                'celular',
                'email'
            ];
            foreach ($requiredFields as $field) {
                if (empty($request->input($field))) {
                    return response()->json(['message' => "Debes completar todos los campos requeridos de la actividad"], 400);
                }
            }

            $admin = SuperAdmin::find($id);
            if ($admin) {
                $admin->nombre = $request->input('nombre');
                $admin->apellido = $request->input('apellido');
                $admin->documento = $request->input('documento');
                $newCelular = $request->input('celular');
                $admin->genero = $request->input('genero');
                $admin->direccion = $request->input('direccion');
                $admin->id_tipo_documento = $request->input('id_tipo_documento');
                $admin->id_departamento = $request->input('id_departamento');
                $admin->id_municipio = $request->input('id_municipio');
                $admin->fecha_nac = $request->input('fecha_nac');
                if ($newCelular && $newCelular !== $admin->celular) {
                    // Verificar si el nuevo email ya está en uso
                    $existing = SuperAdmin::where('celular', $newCelular)->first();
                    if ($existing) {
                        return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 402);
                    }
                    $admin->celular = $newCelular;
                }

                if ($request->hasFile('imagen_perfil')) {
                    //Eliminar el logo anterior
                    Storage::delete(str_replace('storage', 'public', $admin->imagen_perfil));
                    // Guardar el nuevo logo
                    $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                    $admin->imagen_perfil = str_replace('public', 'storage', $path);
                }

                $admin->save();

                if ($admin->auth) {
                    $user = $admin->auth;

                    $password = $request->input('password');
                    if ($password) {
                        $user->password = Hash::make($request->input('password'));
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
                    $user->estado = $request->input('estado');
                    $user->save();
                }
                return response()->json(['message' => 'Superadministrador actualizado correctamente', $admin], 200);
            } else {
                return response()->json(['message' => 'Superadministrador no encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json([
                    'message' => 'No tienes permiso para acceder a esta ruta'
                ], 401);
            }
            $personalizacionKey = 'personalizacion:' . $id;

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
            $personalizacion->imagen_logo = asset('storage/logos/5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.jpg');


            // Guardar los cambios
            $personalizacion->save();
            Redis::set($personalizacionKey, json_encode($personalizacion));
            Redis::expire($personalizacionKey, 432000);

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
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
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
}
