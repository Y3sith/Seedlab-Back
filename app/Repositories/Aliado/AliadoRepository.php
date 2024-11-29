<?php

namespace App\Repositories\Aliado;

use App\Models\Aliado;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;

class AliadoRepository implements AliadoRepositoryInterface
{
    // Trae todos los aliados que están activos (según su estado).
    public function traerAliadosActivos(int $status)
    {
        return Aliado::whereHas('auth', function ($query) use ($status) {
            $query->where('estado', $status);
        })
            ->with(['tipoDato:id,nombre', 'auth:id'])
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', 'id_autentication')
            ->get();
    }

    // Obtiene un aliado por su ID.
    public function traerAliadoxId($id)
    {
        return Aliado::where('id', $id)
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
            ->first();
    }

    // Muestra los aliados según su estado (Activo o Inactivo).
    public function mostrarAliados(string $estado)
    {
        // Convierte el estado en un valor booleano para su uso en la consulta.
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        // Busca los aliados con el estado proporcionado y que sean del rol '3' (asociado con aliados).
        $aliadoVer = DB::table('users')
            ->where('estado', $estadoBool)
            ->where('id_rol', 3)
            ->pluck('id'); // Obtiene una lista de IDs de usuarios (aliados).

        return Aliado::whereIn('id_autentication', $aliadoVer) // Busca aliados que coincidan con los IDs
            ->with(['auth:id,email,estado']) // Trae la relación de autenticación con los campos 'id', 'email' y 'estado'.
            ->get(['id', 'nombre', 'id_autentication']); // Selecciona y devuelve los campos especificados.
    }

    // Obtiene todos los aliados por su ID.
    public function getAllAliados($id)
    {
        return Aliado::where('id', $id)
            ->select('id', 'logo', 'ruta_multi', 'urlpagina', "id_autentication")
            ->first(); // Devuelve el primer resultado que coincida.
    }

    // Trae un aliado por su ID.
    public function traerAliadoPorId(int $id)
    {
        return Aliado::find($id);
    }

    // Crea un nuevo aliado utilizando un procedimiento almacenado.
    public function crearAliado(array $data)
    {

        $logoUrl = $data['logoUrl']['medium']; // Obtiene la URL del logo (tamaño 'medium').

        // Ejecuta un procedimiento almacenado para crear un aliado.
        $results = DB::select('CALL sp_registrar_aliado(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['nombre'],
            $logoUrl,
            $data['descripcion'],
            $data['id_tipo_dato'],
            $data['ruta_multi'],
            $data['urlpagina'],
            $data['email'],
            $data['hashedPassword'], // Contraseña encriptada.
            $data['estado'] === 'true' ? 1 : 0, // Convierte el estado a 1 (activo) o 0 (inactivo).
        ]);

        if (empty($results)) {
            throw new Exception('No se recibió respuesta del procedimiento almacenado');
        }

        return $results[0]; // Devuelve el primer resultado.
    }

    // Edita los datos de un aliado existente.
    public function editarAliado(int $id, array $data)
    {
        $aliado = Aliado::find($id); // Encuentra al aliado por su ID.
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        // Actualiza los campos necesarios en el modelo Aliado
        $aliado->nombre = $data['nombre'] ?? $aliado->nombre;
        $aliado->descripcion = $data['descripcion'] ?? $aliado->descripcion;
        $aliado->id_tipo_dato = $data['id_tipo_dato'] ?? $aliado->id_tipo_dato;
        $aliado->ruta_multi = $data['ruta_multi'] ?? $aliado->ruta_multi;
        $aliado->urlpagina = $data['urlpagina'] ?? $aliado->urlpagina;

        $aliado->save();

        // Actualiza los datos de autenticación (usuario) si es necesario.
        $user = $aliado->auth;

        // Verifica si se actualiza el email.
        if (isset($data['email']) && $data['email'] !== $user->email) {
            // Verifica si el correo ya existe
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                throw new Exception('El correo electrónico ya ha sido registrado anteriormente');
            }
            $user->email = $data['email'];
        }

        // Actualiza la contraseña si está presente
        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Actualiza el estado si está presente
        if (isset($data['estado'])) {
            $user->estado = filter_var($data['estado'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        $user->save(); // Guarda los cambios del usuario.

        return $aliado; // Devuelve el aliado actualizado.
    }

    // Obtiene los asesores asociados a un aliado y filtra por estado.
    public function obtenerAsesoresPorAliado($id, $estado)
    {
        // Busca el aliado por su ID.
        $aliado = Aliado::find($id);
        if (!$aliado) {
            throw new Exception('No se encontró ningún aliado con este ID');
        }

        // Filtra los asesores por el estado de su autenticación (activo/inactivo).
        $asesores = $aliado->asesor()
            ->whereHas('auth', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->select(
                'id',
                'id_aliado',
                'nombre',
                'apellido',
                'imagen_perfil',
                'documento',
                'id_tipo_documento',
                'fecha_nac',
                'direccion',
                'genero',
                'id_municipio',
                'celular',
                'id_autentication'
            )
            ->get(); // Devuelve la lista de asesores.

        return $asesores; // Devuelve los asesores filtrados por estado.
    }


    // Desactiva un aliado cambiando su estado a inactivo.
    public function desactivarAliado(int $id)
    {
        $aliado = Aliado::find($id);// Encuentra el aliado por su ID.
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        $user = $aliado->auth;
        $user->estado = 0;// Cambia el estado del usuario a inactivo.
        $user->save();

        return true;// Devuelve true si la operación fue exitosa.
    }
}
