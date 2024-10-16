<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionCrearUsuario;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Models\PersonalizacionSistema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Aliado;
use App\Services\SuperAdminService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;

class SuperAdminController extends Controller
{

    protected $superAdminService;

    public function __construct(SuperAdminService $superAdminService)
    {
        $this->superAdminService = $superAdminService;
    }


    /**
     * Actualiza la personalización del sistema para un superadmin específico.
     * Solo el rol de superadmin (id_rol = 1) tiene acceso.
     */

    public function personalizacionSis(Request $request, $id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permiso para acceder a esta ruta'], 401);
        }

        $data = $request->all();
        $response = $this->superAdminService->updatePersonalizacion($id, $data);

        return response()->json($response, $response['status']);
    }

    // public function personalizacionSis(Request $request, $id)
    // {
    //     // Verifica si el usuario autenticado es superadmin; de lo contrario, devuelve un error de permisos.
    //     if (Auth::user()->id_rol != 1) {
    //         return response()->json([
    //             'message' => 'No tienes permiso para acceder a esta ruta'
    //         ], 401);
    //     }

    //     // Busca la personalización del sistema por ID.
    //     $personalizacion = PersonalizacionSistema::where('id', $id)->first();
    //     if (!$personalizacion) {
    //         return response()->json([
    //             'message' => 'Personalización no encontrada'
    //         ], 404);
    //     }

    //     // Actualiza los campos de personalización del sistema con los datos recibidos.
    //     $personalizacion->nombre_sistema = $request->input('nombre_sistema');
    //     $personalizacion->color_principal = $request->input('color_principal');
    //     $personalizacion->color_secundario = $request->input('color_secundario');
    //     $personalizacion->id_superadmin = $request->input('id_superadmin');
    //     $personalizacion->descripcion_footer = $request->input('descripcion_footer');
    //     $personalizacion->paginaWeb = $request->input('paginaWeb');
    //     $personalizacion->email = $request->input('email');
    //     $personalizacion->telefono = $request->input('telefono');
    //     $personalizacion->direccion = $request->input('direccion');
    //     $personalizacion->ubicacion = $request->input('ubicacion');

    //     // Maneja la subida del archivo 'logo_footer'.
    //     if ($request->hasFile('logo_footer') && $request->file('logo_footer')->isValid()) {
    //         $logoFooterPath = $request->file('logo_footer')->store('public/logos');
    //         $personalizacion->logo_footer = asset('storage/logos/' . basename($logoFooterPath));
    //     }

    //     // Maneja la subida y conversión de 'imagen_logo' a formato WebP si es necesario.
    //     if ($request->hasFile('imagen_logo') && $request->file('imagen_logo')->isValid()) {
    //         $file = $request->file('imagen_logo');
    //         $fileName = uniqid('logo_') . '.webp';
    //         $folder = 'logos';
    //         $path = "public/$folder/$fileName";

    //         $extension = strtolower($file->getClientOriginalExtension());
    //         if ($extension === 'webp') {
    //             // Si ya es WebP, simplemente mover el archivo
    //             $file->storeAs("public/$folder", $fileName);
    //         } else {
    //             // Convertir a WebP
    //             $sourceImage = $this->createImageFromFile($file->path());
    //             if ($sourceImage) {
    //                 $fullPath = storage_path('app/' . $path);
    //                 // Guardar la imagen como WebP
    //                 imagewebp($sourceImage, $fullPath, 80);
    //                 // Liberar memoria
    //                 imagedestroy($sourceImage);
    //             } else {
    //                 return response()->json(['error' => 'No se pudo procesar la imagen del logo'], 400);
    //             }
    //         }
    //         // Genera la URL completa correctamente
    //         $personalizacion->imagen_logo = asset('storage/' . $folder . '/' . $fileName);
    //     }

    //     // Guarda los cambios en la base de datos.
    //     $personalizacion->save();

    //     return response()->json(['message' => 'Personalización del sistema actualizada correctamente'], 200);
    // }

    /**
     * Crea una imagen a partir del archivo subido.
     * Convierte diferentes tipos de imagen a un formato compatible.
     */
    // private function createImageFromFile($filePath)
    // {
    //     $imageInfo = getimagesize($filePath);
    //     if ($imageInfo === false) {
    //         return false;
    //     }

    //     $mimeType = $imageInfo['mime'];

    //     // Crea una imagen según el tipo MIME detectado.
    //     switch ($mimeType) {
    //         case 'image/jpeg':
    //         case 'image/jpg':
    //             return imagecreatefromjpeg($filePath);
    //         case 'image/png':
    //             return imagecreatefrompng($filePath);
    //         case 'image/gif':
    //             return imagecreatefromgif($filePath);
    //         case 'image/bmp':
    //             return imagecreatefrombmp($filePath);
    //         case 'image/webp':
    //             return imagecreatefromwebp($filePath);
    //         case 'image/x-ms-bmp':  // Algunos sistemas pueden usar este MIME type para BMP
    //             return imagecreatefrombmp($filePath);
    //         case 'image/svg+xml':
    //             // SVG requiere un manejo especial, posiblemente convirtiéndolo a un formato de mapa de bits
    //             return $this->convertSvgToImage($filePath);
    //         default:
    //             return false;
    //     }
    // }

    public function obtenerPersonalizacion($id)
    {
        // Obtener la respuesta del servicio
        $result = $this->superAdminService->obtenerPersonalizacion($id);

        // Verificar si hay un error
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        // Retornar la personalización formateada
        return response()->json($result['data'], $result['status']);
    }

    // public function obtenerPersonalizacion($id)
    // {
    //     //Obtiene la personalización del sistema por su ID.
    //     $personalizacion = PersonalizacionSistema::where('id', $id)->first();

    //     if (!$personalizacion) {
    //         return response()->json([
    //             'message' => 'No se encontraron personalizaciones del sistema'
    //         ], 404);
    //     }
    //     //Devuelve los datos necesarios para ser utilizados en el frontend.
    //     $personalizacionParaCache = [
    //         'imagen_logo' => $personalizacion->imagen_logo ? $this->correctImageUrl($personalizacion->imagen_logo) : null,
    //         'nombre_sistema' => $personalizacion->nombre_sistema,
    //         'color_principal' => $personalizacion->color_principal,
    //         'color_secundario' => $personalizacion->color_secundario,
    //         'descripcion_footer' => $personalizacion->descripcion_footer,
    //         'paginaWeb' => $personalizacion->paginaWeb,
    //         'email' => $personalizacion->email,
    //         'telefono' => $personalizacion->telefono,
    //         'direccion' => $personalizacion->direccion,
    //         'ubicacion' => $personalizacion->ubicacion,
    //     ];

    //     return response()->json($personalizacionParaCache, 200);
    // }

    // /**
    /* Corrige la URL de una imagen, asegurándose de que sea accesible para mostrarse.
    */
    // private function correctImageUrl($path)
    // {
    //     // Si ya es una URL completa, devuélvela directamente
    //     if (filter_var($path, FILTER_VALIDATE_URL)) {
    //         return $path;
    //     }

    //     // Asegúrate de que no haya 'storage/' al principio
    //     $path = ltrim($path, '/'); // Elimina cualquier '/' inicial

    //     // Comprueba si 'storage/' ya está presente
    //     if (strpos($path, 'storage/') !== false) {
    //         // Elimina la parte de 'storage/' si está presente
    //         $path = str_replace('storage/', '', $path);
    //     }

    //     // Devuelve la URL correcta
    //     return url('storage/' . $path);
    // }

    /**
     * Crea un nuevo superadmin, validando los datos ingresados.
     */

    public function crearSuperAdmin(Request $request)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $result = $this->superAdminService->crearSuperAdmin($request);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }
    // public function crearSuperAdmin(Request $data)
    // {


    //     try {
    //         $response = null;
    //         $statusCode = 200;

    //         // Verifica si el usuario tiene permisos de superadmin.
    //         if (Auth::user()->id_rol != 1) {
    //             return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
    //         }

    //         $generateRandomPassword = function($length = 8) {
    //             $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //             $password = '';
    //             for ($i = 0; $i < $length; $i++) {
    //                 $password .= $characters[rand(0, strlen($characters) - 1)];
    //             }
    //             return $password;
    //         };

    //         $randomPassword = $generateRandomPassword();
    //         $hashedPassword = Hash::make($randomPassword);

    //         // Si la direccion y la fecha de nacimiento estan vacias se colocan estos datos por defecto
    //         $direccion = $data->input('direccion', 'Dirección por defecto');
    //         $fecha_nac = $data->input('fecha_nac', '2000-01-01');

    //         $imagen_perfil = null;

    //         if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
    //             $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
    //             $imagen_perfil = Storage::url($imagenPath);
    //         }

    //         // Registra al superadmin utilizando un procedimiento almacenado.
    //         DB::transaction(function () use ($data, &$response, &$statusCode, $direccion, $fecha_nac, $imagen_perfil, $hashedPassword, $randomPassword) {
    //             $results = DB::select('CALL sp_registrar_superadmin(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
    //                 $data['nombre'],
    //                 $data['apellido'],
    //                 $data['documento'],
    //                 $imagen_perfil,
    //                 $data['celular'],
    //                 $data['genero'],
    //                 $direccion,
    //                 $data['id_tipo_documento'],
    //                 $data['departamento'],
    //                 $data['municipio'],
    //                 $fecha_nac,
    //                 $data['email'],
    //                 $hashedPassword,
    //                 $data['estado'],
    //             ]);

    //             if (!empty($results)) {
    //                 $response = $results[0]->mensaje;

    //                 if ($response === 'El correo electrónico ya ha sido registrado anteriormente' || $response === 'El numero de celular ya ha sido registrado en el sistema') {
    //                     $statusCode = 400;
    //                 }else{
    //                     $email = $results[0]->email; 
    //                     $rol = 'Super Admin';
    //                     if ($email) {
    //                         Mail::to($email)->send(new NotificacionCrearUsuario($email, $rol, $randomPassword));
    //                     }essage: 
    //                 }
    //             }
    //         });

    //         return response()->json(['message' => $response], $statusCode);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    //     }
    // }

    /**
     * Obtiene los datos del perfil de un superadmin, junto con la información de su ubicación.
     */

    public function userProfileAdmin($id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permiso para esta función']);
        }
        return $this->superAdminService->userProfileAdmin($id);
    }
    // public function userProfileAdmin($id)
    // {
    //     try {
    //         // Verificar si el usuario autenticado no tiene el rol de SuperAdmin (rol_id 1)
    //         if (Auth::user()->id_rol != 1) {
    //             return response()->json(['message' => 'No tienes permiso para esta función']);
    //         }

    //         // Consultar los datos del SuperAdmin con el ID proporcionado
    //         // Se hace una unión (join) con las tablas 'municipios' y 'departamentos' para obtener nombres y relaciones geográficas
    //         $admin = SuperAdmin::where('superadmin.id', $id)
    //             ->join('municipios', 'superadmin.id_municipio', '=', 'municipios.id')
    //             ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
    //             ->select(
    //                 'superadmin.id', // ID del SuperAdmin
    //                 'superadmin.nombre', // Nombre del SuperAdmin
    //                 'superadmin.apellido', // Apellido del SuperAdmin
    //                 'superadmin.documento', // Documento de identificación
    //                 'superadmin.id_tipo_documento', // Tipo de documento
    //                 'superadmin.fecha_nac', // Fecha de nacimiento
    //                 'superadmin.imagen_perfil', // URL de la imagen de perfil
    //                 'superadmin.direccion', // Dirección
    //                 'superadmin.celular', // Número de celular
    //                 'superadmin.genero', // Género
    //                 'superadmin.id_municipio', // ID del municipio
    //                 'municipios.nombre as municipio_nombre', // Nombre del municipio
    //                 'departamentos.name as departamento_nombre', // Nombre del departamento
    //                 'departamentos.id as id_departamento', // ID del departamento
    //                 'superadmin.id_autentication' // ID de autenticación
    //             )
    //             ->first(); // Obtener el primer resultado

    //         // Retornar un arreglo con los datos del SuperAdmin, transformando algunos campos si es necesario
    //         return [
    //             'id' => $admin->id,
    //             'nombre' => $admin->nombre,
    //             'apellido' => $admin->apellido,
    //             'documento' => $admin->documento,
    //             'id_tipo_documento' => $admin->id_tipo_documento,
    //             'fecha_nac' => $admin->fecha_nac,
    //             'imagen_perfil' => $admin->imagen_perfil ? $this->correctImageUrl($admin->imagen_perfil) : null, // Si tiene imagen de perfil, corregir la URL
    //             'direccion' => $admin->direccion,
    //             'celular' => $admin->celular,
    //             'genero' => $admin->genero,
    //             'id_departamento' => $admin->id_departamento,
    //             'id_municipio' => $admin->id_municipio,
    //             'email' => $admin->auth->email, // Obtener el email desde la relación 'auth'
    //             'estado' => $admin->auth->estado == 1 ? 'Activo' : 'Inactivo', // Comprobar si el estado de autenticación es activo o inactivo
    //             'id_auth' => $admin->id_autentication
    //         ];
    //     } catch (Exception $e) {
    //         // Capturar cualquier excepción y retornar un error con el mensaje
    //         return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    //     }
    // }

    public function mostrarSuperAdmins(Request $request)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 401); // Retornar error si no tiene permiso
        }
        $estado = $request->input('estado', 'Activo');
        return $this->superAdminService->mostrarSuperAdmins($estado);
    }

    // public function mostrarSuperAdmins(Request $request)
    // {
    //     try {
    //         // Verificar si el usuario autenticado tiene el rol de SuperAdmin (id_rol = 1)
    //         if (Auth::user()->id_rol != 1) {
    //             return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 401); // Retornar error si no tiene permiso
    //         }

    //         // Obtener el estado desde el request, con un valor por defecto de 'Activo'
    //         $estado = $request->input('estado', 'Activo');

    //         // Convertir el estado recibido en un valor booleano: 1 para 'Activo' y 0 para 'Inactivo'
    //         $estadoBool = $estado === 'Activo' ? 1 : 0;

    //         // Obtener los IDs de los usuarios que tienen el rol de SuperAdmin (id_rol = 1) y el estado indicado (activo/inactivo)
    //         $adminVer = User::where('estado', $estadoBool)
    //             ->where('id_rol', 1)
    //             ->pluck('id'); // Devuelve una lista de IDs de los usuarios con el estado y rol filtrado

    //         // Obtener los SuperAdmins que coincidan con los IDs de autenticación obtenidos anteriormente
    //         $admins = SuperAdmin::whereIn('id_autentication', $adminVer)
    //             ->with('auth:id,email,estado') // Cargar la relación 'auth' para obtener email y estado
    //             ->get(['id', 'nombre', 'apellido', 'id_autentication']); // Seleccionar solo los campos necesarios

    //         // Recorrer la lista de SuperAdmins y crear un nuevo array con datos detallados (incluyendo email y estado)
    //         $adminsConEstado = $admins->map(function ($admin) {
    //             $user = User::find($admin->id_autentication); // Obtener el usuario relacionado por id_autentication

    //             return [
    //                 'id' => $admin->id, // ID del SuperAdmin
    //                 'nombre' => $admin->nombre, // Nombre del SuperAdmin
    //                 'apellido' => $admin->apellido, // Apellido del SuperAdmin
    //                 'id_auth' => $user->id, // ID de autenticación (relacionado con el User)
    //                 'email' => $user->email, // Email del usuario autenticado
    //                 'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo' // Convertir el estado en texto (Activo o Inactivo)
    //             ];
    //         });

    //         // Retornar la respuesta JSON con los datos de los SuperAdmins, incluyendo los flags JSON_UNESCAPED_UNICODE y JSON_NUMERIC_CHECK
    //         return response()->json($adminsConEstado, 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    //     } catch (Exception $e) {
    //         // En caso de error, capturar la excepción y retornar un mensaje de error con el código 500
    //         return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    //     }
    // }


    /**
     * Actualiza un recurso especificado en el almacenamiento.
     */

    public function editarSuperAdmin(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']); // Retornar error si no tiene permisos
            }
            $response = $this->superAdminService->editarSuperAdmin($request, $id);

            return response()->json(['message' => $response['message'], 'admin' => $response['admin'] ?? null], $response['status']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    // public function editarSuperAdmin(Request $request, $id)
    // {
    //     try {
    //         // Verificar si el usuario autenticado tiene el rol de SuperAdmin (id_rol = 1)
    //         if (Auth::user()->id_rol != 1) {
    //             return response()->json(['message' => 'no tienes permiso para esta funcion']); // Retornar error si no tiene permisos
    //         }

    //         // Lista de campos requeridos para la actualización del SuperAdmin
    //         $requiredFields = [
    //             'nombre',
    //             'apellido',
    //             'documento',
    //             'celular',
    //             'genero',
    //             'direccion',
    //             'id_tipo_documento',
    //             'id_departamento',
    //             'id_municipio',
    //             'fecha_nac',
    //             'celular',
    //             'email'
    //         ];

    //         // Verificar si todos los campos requeridos están presentes en la solicitud
    //         foreach ($requiredFields as $field) {
    //             if (empty($request->input($field))) {
    //                 return response()->json(['message' => "Debes completar todos los campos requeridos."], 400); // Retornar error si algún campo falta
    //             }
    //         }

    //         // Buscar al SuperAdmin por su ID
    //         $admin = SuperAdmin::find($id);

    //         // Si se encuentra al SuperAdmin
    //         if ($admin) {
    //             // Actualizar los campos del SuperAdmin con los datos de la solicitud
    //             $admin->nombre = $request->input('nombre');
    //             $admin->apellido = $request->input('apellido');
    //             $admin->documento = $request->input('documento');
    //             $newCelular = $request->input('celular');
    //             $admin->genero = $request->input('genero');
    //             $admin->direccion = $request->input('direccion');
    //             $admin->id_tipo_documento = $request->input('id_tipo_documento');
    //             $admin->id_departamento = $request->input('id_departamento');
    //             $admin->id_municipio = $request->input('id_municipio');
    //             $admin->fecha_nac = $request->input('fecha_nac');

    //             // Verificar si el nuevo número de celular ya está en uso
    //             if ($newCelular && $newCelular !== $admin->celular) {
    //                 $existing = SuperAdmin::where('celular', $newCelular)->first();
    //                 if ($existing) {
    //                     return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 402);
    //                 }
    //                 $admin->celular = $newCelular; // Actualizar el celular si no está registrado
    //             }

    //             // Verificar si se ha subido una nueva imagen de perfil
    //             if ($request->hasFile('imagen_perfil')) {
    //                 // Eliminar la imagen anterior
    //                 Storage::delete(str_replace('storage', 'public', $admin->imagen_perfil));

    //                 // Guardar la nueva imagen
    //                 $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
    //                 $admin->imagen_perfil = str_replace('public', 'storage', $path); // Actualizar la ruta de la imagen
    //             }

    //             // Guardar los cambios del SuperAdmin
    //             $admin->save();

    //             // Verificar si el SuperAdmin tiene una relación con el modelo User (auth)
    //             if ($admin->auth) {
    //                 $user = $admin->auth;

    //                 // Actualizar la contraseña si se proporciona
    //                 $password = $request->input('password');
    //                 if ($password) {
    //                     $user->password = Hash::make($password); // Encriptar y actualizar la contraseña
    //                 }

    //                 // Verificar si el nuevo correo electrónico ya está en uso
    //                 $newEmail = $request->input('email');
    //                 if ($newEmail && $newEmail !== $user->email) {
    //                     $existingUser = User::where('email', $newEmail)->first();
    //                     if ($existingUser) {
    //                         return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
    //                     }
    //                     $user->email = $newEmail; // Actualizar el email si no está registrado
    //                 }

    //                 // Actualizar el estado del usuario (activo o inactivo)
    //                 $user->estado = $request->input('estado');
    //                 $user->save(); // Guardar los cambios en el modelo User
    //             }

    //             // Respuesta exitosa indicando que el SuperAdmin ha sido actualizado correctamente
    //             return response()->json(['message' => 'Superadministrador actualizado correctamente', $admin], 200);
    //         } else {
    //             // Si no se encuentra el SuperAdmin, retornar error 404
    //             return response()->json(['message' => 'Superadministrador no encontrado'], 404);
    //         }
    //     } catch (Exception $e) {
    //         // Capturar cualquier excepción y retornar un mensaje de error
    //         return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    //     }
    // }

    public function restore($id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para acceder a esta ruta'], 401);
            }
            $response = $this->superAdminService->restore($id);
            return response()->json($response['message'], $response['status']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar la personalización',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function restore($id)
    // {
    //     try {
    //         if (Auth::user()->id_rol != 1) {
    //             return response()->json([
    //                 'message' => 'No tienes permiso para acceder a esta ruta'
    //             ], 401);
    //         }
    //         $personalizacionKey = 'personalizacion:' . $id;

    //         // Buscar la personalización por su ID
    //         $personalizacion = PersonalizacionSistema::find($id);

    //         if (!$personalizacion) {
    //             return response()->json([
    //                 'message' => 'Personalización no encontrada'
    //             ], 404);
    //         }

    //         // Restaurar los valores originales (puedes definir los valores originales manualmente o tenerlos guardados previamente)
    //         $personalizacion->nombre_sistema = 'SeedLab';
    //         $personalizacion->color_principal = '#00B3ED';
    //         $personalizacion->color_secundario = '#FA7D00';
    //         $personalizacion->descripcion_footer = 'Este programa estará enfocado en emprendimientos de base tecnológica, para ideas validadas, que cuenten con un codesarrollo, prototipado y pruebas de concepto. Se va a abordar en temas como Big Data, ciberseguridad e IA, herramientas de hardware y software, inteligencia competitiva, vigilancia tecnológica y propiedad intelectual.';
    //         $personalizacion->paginaWeb = 'seedlab.com';
    //         $personalizacion->email = 'email@seedlab.com';
    //         $personalizacion->telefono = '(55) 5555-5555';
    //         $personalizacion->direccion = 'Calle 48 # 28 - 40';
    //         $personalizacion->ubicacion = 'Bucaramanga, Santander, Colombia';
    //         $personalizacion->imagen_logo = asset('storage/logos/5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.webp');


    //         // Guardar los cambios
    //         $personalizacion->save();


    //         return response()->json([
    //             'message' => 'Personalización restaurada correctamente',
    //             $personalizacion
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'message' => 'Error al restaurar la personalización',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    //revisar si se puede utilizar el listar aliados que esta en aliados
    public function listarAliados()
    {
        try {
            // Verifica si el usuario autenticado tiene uno de los roles permitidos (1, 3 o 4)
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permiso para esta funcion'], 400); // Retorna un error si no tiene permisos
            }

            // Consulta los aliados que tienen el estado '1' (activo)
            $aliados = Aliado::whereHas('auth', function ($query) {
                $query->where('estado', '1'); // Filtra aliados cuyo estado sea activo
            })->get(['id', 'nombre']); // Obtiene solo los campos 'id' y 'nombre' de los aliados activos

            // Retorna la lista de aliados en formato JSON con código de estado 200 (éxito)
            return response()->json($aliados, 200);
        } catch (Exception $e) {
            // Si ocurre un error, lo captura y devuelve un mensaje de error con un código de estado 401
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
        }
    }
}
