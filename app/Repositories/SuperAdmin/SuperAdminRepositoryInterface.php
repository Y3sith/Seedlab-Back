<?php

namespace App\Repositories\SuperAdmin;



interface SuperAdminRepositoryInterface{
    

    public function findPersonalizacionById($id);
    public function updatePersonalizacion($id, array $data);
    public function restorePersonalizacion($id);
    public function registrarSuperAdmin($data, $hashedPassword, $direccion, $fecha_nac, $imagen_perfil);
    public function getUserProfileById($id);
    public function getSuperAdminsByState($estado);
    public function findSuperAdminById($id);
    public function updateSuperadmin($id, array $data);
    public function getAliadosActividad();

}