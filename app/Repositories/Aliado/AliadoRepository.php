<?php

namespace App\Repositories\Aliado;

use App\Models\Aliado;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AliadoRepository implements AliadoRepositoryInterface
{
    public function traerAliadosActivos(int $status)
    {
        return Aliado::whereHas('auth', function ($query) use ($status) {
            $query->where('estado', $status);
        })
            ->with(['tipoDato:id,nombre', 'auth:id'])
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', 'id_autentication')
            ->get();
    }

    public function traerAliadoxId($id)
    {
        return Aliado::where('id', $id)
            ->select('id', 'nombre', 'descripcion', 'logo', 'ruta_multi', 'urlpagina', 'id_tipo_dato', "id_autentication")
            ->first();
    }

    public function mostrarAliados(string $estado)
    {
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        $aliadoVer = DB::table('users')
            ->where('estado', $estadoBool)
            ->where('id_rol', 3)
            ->pluck('id');

        return Aliado::whereIn('id_autentication', $aliadoVer)
            ->with(['auth:id,email,estado'])
            ->get(['id', 'nombre', 'id_autentication']);
    }

    public function getAllAliados($id)
    {
        return Aliado::where('id', $id)
            ->select('id', 'logo', 'ruta_multi', 'urlpagina', "id_autentication")
            ->first();
    }

    public function traerAliadoPorId(int $id)
    {
        return Aliado::find($id);
    }

    public function crearAliado(array $data)
    {
        
        $logoUrl = $data['logoUrl']['medium'];

        $results = DB::select('CALL sp_registrar_aliado(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['nombre'],
            $logoUrl,
            $data['descripcion'],
            $data['id_tipo_dato'],
            $data['ruta_multi'],
            $data['urlpagina'],
            $data['email'],
            $data['hashedPassword'],
            $data['estado'] === 'true' ? 1 : 0,
        ]);

        if (empty($results)) {
            throw new Exception('No se recibió respuesta del procedimiento almacenado');
        }

        return $results[0];
    }

    public function editarAliado(int $id, array $data)
    {
        $aliado = Aliado::find($id);
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

        // Actualiza la relación auth (usuario) si es necesario
        $user = $aliado->auth;

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

        $user->save();

        return $aliado;
    }

    public function obtenerAsesoresPorAliado($id, $estado)
    {
        // Busca el aliado
        $aliado = Aliado::find($id);
        if (!$aliado) {
            throw new Exception('No se encontró ningún aliado con este ID');
        }
    
        // Filtra los asesores del aliado según el estado
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
            ->get();
    
        return $asesores;
    }
    


    public function desactivarAliado(int $id)
    {
        $aliado = Aliado::find($id);
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        $user = $aliado->auth;
        $user->estado = 0;
        $user->save();

        return true;
    }
}
