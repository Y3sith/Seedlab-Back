<?php

namespace App\Services;

use App\Jobs\EnviarNotificacionCrearUsuario;
use App\Models\User;
use App\Repositories\SuperAdmin\SuperAdminRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuperAdminService
{

    protected $superAdminRepository;
    protected $imageService;

    public function __construct(SuperAdminRepositoryInterface $repository, ImageService $imageService)
    {
        $this->superAdminRepository = $repository;
        $this->imageService = $imageService;
    }

    public function updatePersonalizacion($id, $data)
    {
        // Verifica si la personalización existe
        $personalizacion = $this->superAdminRepository->findPersonalizacionById($id);
        if (!$personalizacion) {
            return ['error' => 'Personalización no encontrada', 'status' => 404];
        }

        // Manejo del logo_footer utilizando el servicio de imágenes
        if (isset($data['logo_footer']) && $data['logo_footer']->isValid()) {
            $logoFooterPath = $this->imageService->procesarImagen($data['logo_footer'], 'logos');
            $data['logo_footer'] = asset('storage/' . $logoFooterPath);
        }

        // Manejo del imagen_logo utilizando el servicio de imágenes
        if (isset($data['imagen_logo']) && $data['imagen_logo']->isValid()) {
            $imagenLogoPath = $this->imageService->procesarImagen($data['imagen_logo'], 'logos');
            $data['imagen_logo'] = asset('storage/' . $imagenLogoPath);
        }

        // Actualizar los datos
        $this->superAdminRepository->updatePersonalizacion($id, $data);
        return ['message' => 'Personalización del sistema actualizada correctamente', 'status' => 200];
    }

    public function obtenerPersonalizacion($id)
    {
        // Obtener la personalización a través del repositorio
        $personalizacion = $this->superAdminRepository->findPersonalizacionById($id);

        // Verificar si se encontró la personalización
        if (!$personalizacion) {
            return ['error' => 'No se encontraron personalizaciones del sistema', 'status' => 404];
        }

        // Formatear los datos para el frontend
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

        return ['data' => $personalizacionParaCache, 'status' => 200];
    }

    // Función auxiliar para corregir la URL de la imagen
    protected function correctImageUrl($imageUrl)
    {
        return asset('storage/logos/' . basename($imageUrl));
    }

    public function crearSuperAdmin($data)
    {
        try {

            // Genera la contraseña aleatoria
            $originalPassword = $data['password'];
            $randomPassword = $this->generateRandomPassword();
            $hashedPassword = Hash::make($randomPassword);

            // Establece los valores predeterminados si no se proporcionan
            $direccion = $data->input('direccion', 'Dirección por defecto');
            $fecha_nac = $data->input('fecha_nac', '2000-01-01');

            // Procesa la imagen de perfil
            $imagen_perfil = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
                $imagen_perfil = Storage::url($imagenPath);
            }

            // Llama al repositorio para registrar al superadmin
            $results = $this->superAdminRepository->registrarSuperAdmin($data, $hashedPassword, $direccion, $fecha_nac, $imagen_perfil);

            if (!empty($results)) {
                $response = $results[0]->mensaje;

                if ($response === 'El correo electrónico ya ha sido registrado anteriormente' || $response === 'El numero de celular ya ha sido registrado en el sistema') {
                    return ['message' => $response, 'status' => 400];
                } else {
                    $email = $results[0]->email;

                    // Envia el correo utilizando un Job
                    if ($email) {
                        Log::info('Despachando job para enviar notificación', ['email' => $email]);
                        EnviarNotificacionCrearUsuario::dispatch($email, 'Aliado', $originalPassword);
                    }


                    return ['message' => 'SuperAdmin registrado correctamente', 'status' => 200];
                }
            }
        } catch (Exception $e) {
            return ['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage(), 'status' => 500];
        }
    }

    protected function generateRandomPassword(int $length = 8): string
    {
        return Str::random($length);
    }

    public function userProfileAdmin($id)
    {
        try {
            $admin = $this->superAdminRepository->getUserProfileById($id);

            if (!$admin) {
                return response()->json(['error' => 'SuperAdmin no encontrado'], 404);
            }

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

    public function mostrarSuperAdmins($estado)
    {
        try {
            $admins = $this->superAdminRepository->getSuperAdminsByState($estado);

            // Recorrer la lista de SuperAdmins y crear un nuevo array con datos detallados (incluyendo email y estado)
            $adminsConEstado = $admins->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'nombre' => $admin->nombre,
                    'apellido' => $admin->apellido,
                    'id_auth' => $admin->auth->id,
                    'email' => $admin->auth->email,
                    'estado' => $admin->auth->estado == 1 ? 'Activo' : 'Inactivo'
                ];
            });

            return response()->json($adminsConEstado, 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function editarSuperAdmin($request, $id)
    {
        
        // Validar campos requeridos
        $requiredFields = [
            'nombre', 'apellido', 'documento', 'celular', 
            'genero', 'direccion', 'id_tipo_documento', 
            'id_departamento', 'id_municipio', 'fecha_nac', 
            'email'
        ];

        foreach ($requiredFields as $field) {
            if (empty($request->input($field))) {
                return ['status' => 400, 'message' => "Debes completar todos los campos requeridos."];
            }
        }

        // Buscar y actualizar SuperAdmin
        $admin = $this->superAdminRepository->updateSuperadmin($id, $request->all());

        if (!$admin) {
            return ['status' => 404, 'message' => 'Superadministrador no encontrado'];
        }

        // Actualizar usuario asociado si existe
        if ($admin->auth) {
            $user = $admin->auth;

            // Actualizar la contraseña si se proporciona
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            // Verificar si el nuevo correo electrónico ya está en uso
            $newEmail = $request->input('email');
            if ($newEmail && $newEmail !== $user->email) {
                $existingUser = User::where('email', $newEmail)->first();
                if ($existingUser) {
                    return ['status' => 400, 'message' => 'El correo electrónico ya ha sido registrado anteriormente'];
                }
                $user->email = $newEmail;
            }

            $user->estado = $request->input('estado');
            $user->save();
        }

        return ['status' => 200, 'message' => 'Superadministrador actualizado correctamente', 'admin' => $admin];
    }

    public function restore($id)
    {
        $personalizacion = $this->superAdminRepository->restorePersonalizacion($id);

        if (!$personalizacion) {
            return ['status' => 404, 'message' => 'Personalización no encontrada'];
        }

        return ['status' => 200, 'message' => 'Personalización restaurada correctamente', 'data' => $personalizacion];
    }
}
