<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Mail\VerificationCodeEmail;
use App\Models\Emprendedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Token;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // Validar los datos de entrada del usuario
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar al usuario en la base de datos usando el email, incluyendo la relación con emprendedor
        $user = User::where('email', $request->email)->with('emprendedor')->first();

        // Si no se encuentra el usuario, devolver un mensaje de error con estado 404
        if (!$user) {
            return response()->json(['message' => 'Tu usuario no existe en el sistema'], 404);
        }

        // Verificar que el usuario tenga un rol permitido y que esté activo
        if ($user->id_rol != 5 && $user->estado != 1) {
            return response()->json(['message' => 'Tu usuario no está activo en el sistema actualmente'], 401);
        }

        // Verificar si la contraseña proporcionada es correcta
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Tu contraseña es incorrecta'], 410);
        }

        // Verificar si el usuario tiene una contraseña temporal
        if ($user->is_temporary_password) {
            // Asegurarse de que 'temporary_password_created_at' no sea null
            if (!$user->temporary_password_created_at) {
                return response()->json(['message' => 'Información de contraseña temporal inválida.'], 500);
            }

            // Verificar si 'temporary_password_created_at' es una instancia de Carbon
            if (!$user->temporary_password_created_at instanceof Carbon) {
                // Parsear manualmente la fecha si no lo es
                try {
                    $createdAt = Carbon::parse($user->temporary_password_created_at);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Error al procesar la fecha de la contraseña temporal.'], 500);
                }
            } else {
                $createdAt = $user->temporary_password_created_at;
            }

            // Obtener la fecha y hora actual
            $now = Carbon::now();

            // Verificar si han pasado más de 20 minutos desde la creación de la contraseña temporal
            if ($createdAt->diffInMinutes($now) > 20) {
                return response()->json([
                    'message' => 'La contraseña temporal ha expirado. Por favor, solicita una nueva.'
                ], 403);
            }
        }

        // Si el usuario tiene rol 5, verificar si su correo electrónico está verificado
        if ($user->id_rol == 5) {
            if ($user->emprendedor->email_verified_at) {
                // Si el correo está verificado, actualizar el estado del usuario
                $user->estado = 1;
                $user->save();
            } else {
                // Si no está verificado, generar un código de verificación y enviarlo por correo
                $verificationCode = mt_rand(10000, 99999);
                $user->emprendedor->cod_ver = $verificationCode;
                $user->emprendedor->save();
                // Enviar el código de verificación por correo
                Mail::to($user->email)->send(new VerificationCodeEmail($verificationCode));
                return response()->json(['message' => 'Por favor verifique su correo electrónico'], 409);
            }
        }

        // Establecer la zona horaria por defecto
        Carbon::setLocale('es');
        date_default_timezone_set('America/Bogota');

        // Crear un token de acceso para el usuario
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        // Se establece la expiración del token
        $token->expires_at = Carbon::now()->addMinutes(1);
        $token->save();

        

        // Obtener información adicional del usuario
        $additionalInfo = $this->getAdditionalInfo($user);

        // Devolver el token de acceso y la información del usuario en formato JSON
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'user' => $additionalInfo,
        ], 200);
    }


    protected function getAdditionalInfo($user)
    {
        // Inicializa un array vacío para almacenar información adicional del usuario
        $info = [];

        // Utiliza un switch para determinar el tipo de usuario según su rol
        switch ($user->id_rol) {
            case 1: // Rol de superadmin
                $info = [
                    'id' => $user->superadmin->id, // ID del superadmin
                    'nombre' => $user->superadmin->nombre, // Nombre del superadmin
                    'apellido' => $user->superadmin->apellido, // Apellido del superadmin
                    'id_autentication' => $user->superadmin->id_autentication, // ID de autenticación del superadmin
                    'id_rol' => $user->id_rol, // ID del rol
                ];
                break;
            case 2: // Rol de orientador
                $info = [
                    'id' => $user->orientador->id, // ID del orientador
                    'nombre' => $user->orientador->nombre, // Nombre del orientador
                    'apellido' => $user->orientador->apellido, // Apellido del orientador
                    'id_autentication' => $user->orientador->id_autentication, // ID de autenticación del orientador
                    'id_rol' => $user->id_rol, // ID del rol
                ];
                break;
            case 3: // Rol de aliado
                $info = [
                    'id' => $user->aliado->id, // ID del aliado
                    'nombre' => $user->aliado->nombre, // Nombre del aliado
                    'id_autentication' => $user->aliado->id_autentication, // ID de autenticación del aliado
                    'id_rol' => $user->id_rol, // ID del rol
                ];
                break;
            case 4: // Rol de asesor
                $info = [
                    'id' => $user->asesor->id, // ID del asesor
                    'id_autentication' => $user->asesor->id_autentication, // ID de autenticación del asesor
                    'id_aliado' => $user->asesor->id_aliado, // ID del aliado asociado al asesor
                    'id_rol' => $user->id_rol, // ID del rol
                ];
                break;
            case 5: // Rol de mprendedor
                $info = $user; // Devuelve toda la información del usuario
                break;
            default: // Para cualquier otro rol no especificado
                $info = []; // Mantiene el array vacío
                break;
        }

        // Devuelve la información adicional del usuario
        return $info;
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial del path para evitar duplicados
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path); // Devuelve la URL completa del archivo de imagen
    }

    public function userProfileEmprendedor($documento)
    {
        // Verifica que el usuario autenticado tenga rol 5 (emprendedor)
        if (Auth::user()->id_rol != 5) {
            // Si no tiene permisos, devuelve un mensaje de error con estado 401
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        // Busca al emprendedor en la base de datos usando el documento
        $emprendedor = Emprendedor::where('documento', $documento)
            ->select('nombre', 'apellido', 'imagen_perfil', 'documento', 'celular', 'genero', 'fecha_nac', 'direccion', 'id_departamento', 'id_municipio', 'id_autentication', 'id_tipo_documento')
            ->first();

        // Devuelve un array con la información del emprendedor
        return [
            'id' => $emprendedor->auth->id,
            'nombre' => $emprendedor->nombre,
            'apellido' => $emprendedor->apellido,
            'imagen_perfil' => $emprendedor->imagen_perfil ? $this->correctImageUrl($emprendedor->imagen_perfil) : null,
            'documento' => $emprendedor->documento,
            'celular' => $emprendedor->celular,
            'genero' => $emprendedor->genero,
            'fecha_nac' => $emprendedor->fecha_nac,
            'direccion' => $emprendedor->direccion,
            'id_departamento' => $emprendedor->id_departamento,
            'id_municipio' => $emprendedor->id_municipio,
            'id_autentication' => $emprendedor->id_autentication,
            'id_tipo_documento' => $emprendedor->id_tipo_documento,
            'email' => $emprendedor->auth->email,
            'estado' => $emprendedor->auth->estado == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function logout(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        if ($user) {
            // Obtener el token desde el encabezado de autorización
            $token = $request->bearerToken();

            // Buscar y eliminar el token
            $tokenModel = Token::where('id', $token)->first();

            if ($tokenModel) {
                $tokenModel->delete();
                return response()->json(['message' => 'Successfully logged out']);
            }

            return response()->json(['message' => 'Token not found'], 404);
        }

        return response()->json(['message' => 'User not found'], 404);
    }




    protected function existeusuario($documento)
    {
        // Busca en la base de datos un emprendedor con el documento proporcionado
        $valuser = Emprendedor::where('documento', $documento)->first();

        // Si se encuentra un usuario con ese documento, retorna un mensaje indicando que ya existe
        if ($valuser) {
            return 'Tu documento ya existe en el sistema';
        } else {
            // Si no se encuentra, retorna null indicando que el documento es único
            return null;
        }
    }

    protected function register(Request $data)
    {
        // Inicializa la respuesta y el código de estado
        $response = null;
        $statusCode = 200;

        // Genera un código de verificación aleatorio de 5 dígitos
        $verificationCode = mt_rand(10000, 99999);

        // Verifica que la contraseña tenga al menos 8 caracteres
        if (strlen($data['password']) < 8) {
            return response()->json(['message' => 'La contraseña debe tener al menos 8 caracteres'], 400);
        }

        // Inicia una transacción de base de datos
        DB::transaction(function () use ($data, $verificationCode, &$response, &$statusCode) {
            // Llama a un procedimiento almacenado para registrar al emprendedor
            $results = DB::select('CALL sp_registrar_emprendedor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['documento'],
                $data['id_tipo_documento'],
                $data['nombre'],
                $data['apellido'],
                $data['imagen_perfil'],
                $data['celular'],
                $data['genero'],
                $data['fecha_nacimiento'],
                $data['id_departamento'],
                $data['id_municipio'],
                $data['direccion'],
                $data['email'],
                Hash::make($data['password']),
                $data['estado'],
                $verificationCode,
            ]);

            // Verifica si hay resultados del procedimiento almacenado
            if (!empty($results)) {
                $response = $results[0]->mensaje;

                // Verifica si el mensaje indica que el documento, correo o celular ya están registrados
                if (
                    $response === 'El numero de documento ya ha sido registrado en el sistema' ||
                    $response === 'El correo electrónico ya ha sido registrado anteriormente' ||
                    $response === 'Este numero de celular ya ha sido registrado anteriormente'
                ) {
                    $statusCode = 400; // Cambia el código de estado a 400 para errores de validación
                } else {
                    // Envía un correo electrónico con el código de verificación
                    Mail::to($data['email'])->send(new VerificationCodeEmail($verificationCode));
                }
            }
        });

        // Devuelve una respuesta JSON con el mensaje y el correo electrónico, junto con el código de estado
        return response()->json(['message' => $response, 'email' => $data['email']], $statusCode);
    }

    protected function validate_email(Request $request)
    {
        // Inicializa la respuesta y el código de estado
        $response = null;
        $statusCode = 200;

        // Inicia una transacción de base de datos
        DB::transaction(function () use ($request, &$response, &$statusCode) {
            // Llama a un procedimiento almacenado para validar el correo y el código de verificación
            $results = DB::select('CALL sp_validar_correo(?,?)', [
                $request['email'],
                $request['codigo'],
            ]);

            // Verifica si hay resultados del procedimiento almacenado
            if (!empty($results)) {
                $response = $results[0]->mensaje;

                // Verifica el mensaje para establecer el código de estado adecuado
                if ($response === 'El correo electrónico no esta registrado' || $response === 'El código de verificación proporcionado no coincide') {
                    $statusCode = 400; // Cambia el código de estado a 400 para errores de validación
                } elseif ($response === 'El correo electrónico ya ha sido verificado anteriormente') {
                    $statusCode = 409; // Cambia el código de estado a 409 para conflictos (ya verificado)
                }
            }
        });

        // Devuelve una respuesta JSON con el mensaje y el código de estado
        return response()->json(['message' => $response], $statusCode);
    }

    /*Metodo que maneja el envio del correo para restablecer la contraseña
     */
    public function enviarRecuperarContrasena(Request $request)
    {
        // Obtiene el correo electrónico del request
        $email = $request->email;

        // Verifica si se ha proporcionado un correo electrónico
        if (!$email) {
            return response()->json(['message' => 'Por favor, proporciona un correo'], 400);
        }

        // Busca al usuario en la base de datos por su correo electrónico
        $user = User::where('email', $email)->first();

        // Verifica si el usuario existe
        if (!$user) {
            return response()->json(['message' => 'Esta cuenta no existe'], 404);
        }

        // Genera una nueva contraseña temporal aleatoria de 10 caracteres
        $temporaryPassword = Str::random(10);

        // Asigna la nueva contraseña temporal al usuario (debe ser hasheada)
        $user->password = Hash::make($temporaryPassword);
        // Establece la fecha de creación de la contraseña temporal
        $user->temporary_password_created_at = now();
        // Marca al usuario como que tiene una contraseña temporal
        $user->is_temporary_password = true;
        // Guarda los cambios en el usuario
        $user->save();

        // Envía un correo electrónico al usuario con la nueva contraseña temporal
        Mail::to($email)->send(new PasswordReset($temporaryPassword));

        // Devuelve una respuesta indicando que se ha enviado el correo
        return response()->json(['message' => 'Te hemos enviado un email con tu nueva contraseña temporal. Cámbiala cuando inicies sesión.'], 200);
    }
}
