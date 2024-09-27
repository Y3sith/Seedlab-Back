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
     * Actualiza la personalización del sistema para un superadmin específico.
     * Solo el rol de superadmin (id_rol = 1) tiene acceso.
     */
    public function personalizacionSis(Request $request, $id)
    {
        // Verifica si el usuario autenticado es superadmin; de lo contrario, devuelve un error de permisos.
        if (Auth::user()->id_rol != 1) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        // Busca la personalización del sistema por ID.
        $personalizacion = PersonalizacionSistema::where('id', $id)->first();
        if (!$personalizacion) {
            return response()->json([
                'message' => 'Personalización no encontrada'
            ], 404);
        }

        // Actualiza los campos de personalización del sistema con los datos recibidos.
        $personalizacion->nombre_sistema = $request->input('nombre_sistema');
        $personalizacion->color_principal = $request->input('color_principal');
        $personalizacion->color_secundario = $request->input('color_secundario');
        $personalizacion->id_superadmin = $request->input('id_superadmin');
        $personalizacion->descripcion_footer = $request->input('descripcion_footer');
        $personalizacion->paginaWeb = $request->input('paginaWeb');
        $personalizacion->email = $request->input('email');
        $personalizacion->telefono = $request->input('telefono');
        $personalizacion->direccion = $request->input('direccion');
        $personalizacion->ubicacion = $request->input('ubicacion');

        // Maneja la subida del archivo 'logo_footer'.
        if ($request->hasFile('logo_footer') && $request->file('logo_footer')->isValid()) {
            $logoFooterPath = $request->file('logo_footer')->store('public/logos');
            $personalizacion->logo_footer = asset('storage/logos/' . basename($logoFooterPath));
        }

        // Maneja la subida y conversión de 'imagen_logo' a formato WebP si es necesario.
        if ($request->hasFile('imagen_logo') && $request->file('imagen_logo')->isValid()) {
            $file = $request->file('imagen_logo');
            $fileName = uniqid('logo_') . '.webp';
            $folder = 'logos';
            $path = "public/$folder/$fileName";

            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension === 'webp') {
                $file->storeAs("public/$folder", $fileName);
            } else {
                $sourceImage = $this->createImageFromFile($file->path());
                if ($sourceImage) {
                    $fullPath = storage_path('app/' . $path);
                    imagewebp($sourceImage, $fullPath, 80);
                    imagedestroy($sourceImage);
                } else {
                    return response()->json(['error' => 'No se pudo procesar la imagen del logo'], 400);
                }
            }
            $personalizacion->imagen_logo = asset('storage/' . $folder . '/' . $fileName);
        }

        // Guarda los cambios en la base de datos.
        $personalizacion->save();

        return response()->json(['message' => 'Personalización del sistema actualizada correctamente'], 200);
    }

    /**
     * Crea una imagen a partir del archivo subido.
     * Convierte diferentes tipos de imagen a un formato compatible.
     */
    private function createImageFromFile($filePath)
    {
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        // Crea una imagen según el tipo MIME detectado.
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/bmp':
                return imagecreatefrombmp($filePath);
            default:
                return false;
        }
    }

    public function obtenerPersonalizacion($id)
    { 
        //Obtiene la personalización del sistema por su ID.
        $personalizacion = PersonalizacionSistema::where('id', $id)->first();

        if (!$personalizacion) {
            return response()->json([
                'message' => 'No se encontraron personalizaciones del sistema'
            ], 404);
        }
        //Devuelve los datos necesarios para ser utilizados en el frontend.
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

    /**
     * Corrige la URL de una imagen, asegurándose de que sea accesible para mostrarse.
     */
    private function correctImageUrl($path)
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (strpos($path, 'storage/') !== false) {
            $path = str_replace('storage/', '', $path);
        }

        return url('storage/' . $path);
    }

    /**
     * Crea un nuevo superadmin, validando los datos ingresados.
     */
    public function crearSuperAdmin(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;

            // Verifica si el usuario tiene permisos de superadmin.
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Valida que la contraseña tenga al menos 8 caracteres.
            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }

            // Si la direccion y la fecha de nacimiento estan vacias se colocan estos datos por defecto
            $direccion = $data->input('direccion', 'Dirección por defecto');
            $fecha_nac = $data->input('fecha_nac', '2000-01-01');

            $imagen_perfil = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
                $imagen_perfil = Storage::url($imagenPath);
            }

            // Registra al superadmin utilizando un procedimiento almacenado.
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
     * Obtiene los datos del perfil de un superadmin, junto con la información de su ubicación.
     */
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
                    'superadmin.email',
                    'superadmin.genero',
                    'superadmin.celular',
                    'superadmin.fecha_nac',
                    'superadmin.imagen_perfil',
                    'municipios.nombre_municipio as municipio',
                    'departamentos.nombre_departamento as departamento'
                )
                ->first();

            //si el id del superadmin es diferente a los que estan registrados salta el error
            if (!$admin) {
                return response()->json(['error' => 'No se encontró el superadmin'], 404);
            }

            return response()->json($admin);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el perfil del superadmin: ' . $e->getMessage()], 500);
        }
    }
}
