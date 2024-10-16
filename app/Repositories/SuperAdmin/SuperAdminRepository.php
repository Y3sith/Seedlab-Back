<?php

namespace App\Repositories\SuperAdmin;

use App\Models\PersonalizacionSistema;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Repositories\SuperAdmin\SuperAdminRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuperAdminRepository implements SuperAdminRepositoryInterface{

    public function findPersonalizacionById($id)
    {
        return PersonalizacionSistema::find($id);
    }

    public function updatePersonalizacion($id, array $data)
    {
        $personalizacion = PersonalizacionSistema::find($id);
        if ($personalizacion) {
            $personalizacion->update($data);
        }
        return $personalizacion;
    }

    public function registrarSuperAdmin($data, $hashedPassword, $direccion, $fecha_nac, $imagen_perfil)
    {
        return DB::select('CALL sp_registrar_superadmin(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
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
            $hashedPassword,
            $data['estado'],
        ]);
    }

    public function getUserProfileById($id)
    {
        return SuperAdmin::where('superadmin.id', $id)
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
    }

    public function getSuperAdminsByState($estado)
    {
        $estadoBool = $estado === 'Activo' ? 1 : 0;

        // Obtener los IDs de los usuarios que tienen el rol de SuperAdmin (id_rol = 1) y el estado indicado (activo/inactivo)
        $adminVer = User::where('estado', $estadoBool)
            ->where('id_rol', 1)
            ->pluck('id');

        // Obtener los SuperAdmins que coincidan con los IDs de autenticación obtenidos anteriormente
        return SuperAdmin::whereIn('id_autentication', $adminVer)
            ->with('auth:id,email,estado') // Cargar la relación 'auth' para obtener email y estado
            ->get(['id', 'nombre', 'apellido', 'id_autentication']);
    }

    public function findSuperAdminById($id)
    {
        return SuperAdmin::find($id);
    }

    public function updateSuperadmin($id, array $data)
    {
        $admin = $this->findSuperAdminById($id);
        
        if (!$admin) {
            return null;
        }

        // Actualizar campos del SuperAdmin
        $admin->fill($data);
        
        // Verificar imagen de perfil
        if (isset($data['imagen_perfil']) && isset($admin->imagen_perfil)) {
            Storage::delete(str_replace('storage', 'public', $admin->imagen_perfil));
            $path = $data['imagen_perfil']->store('public/fotoPerfil');
            $admin->imagen_perfil = str_replace('public', 'storage', $path);
        }

        $admin->save();

        return $admin;
    }

    public function restorePersonalizacion($id)
    {
        $personalizacion = $this->findPersonalizacionById($id);

        if (!$personalizacion) {
            return null;
        }

        // Restaurar valores originales
        $personalizacion->nombre_sistema = 'SeedLab';
        $personalizacion->color_principal = '#00B3ED';
        $personalizacion->color_secundario = '#FA7D00';
        $personalizacion->descripcion_footer = 'Este programa estará enfocado en emprendimientos de base tecnológica, para ideas validadas, que cuenten con un codesarrollo, prototipado y pruebas de concepto. Se va a abordar en temas como Big Data, ciberseguridad e IA, herramientas de hardware y software, inteligencia competitiva, vigilancia tecnológica y propiedad intelectual.';
        $personalizacion->paginaWeb = 'seedlab.com';
        $personalizacion->email = 'email@seedlab.com';
        $personalizacion->telefono = '(55) 5555-5555';
        $personalizacion->direccion = 'Calle 48 # 28 - 40';
        $personalizacion->ubicacion = 'Bucaramanga, Santander, Colombia';
        $personalizacion->imagen_logo = asset('storage/logos/5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.webp');

        // Guardar los cambios
        $personalizacion->save();

        return $personalizacion;
    }
}