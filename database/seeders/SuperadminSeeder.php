<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Artisan::call('storage:link'); 

        $fotoPerfilPath = storage_path('app/public/fotoPerfil');
        if (!File::exists($fotoPerfilPath)) {
            File::makeDirectory($fotoPerfilPath, 0755, true);
        }
                
        SuperAdmin::create([
            "nombre"=> "SuperAdmin",
            "apellido"=> "Prueba",
            "documento"=> "000000001010",
            "direccion"=>"Direccion por defecto",
            "fecha_nac"=>"2000-05-21",
            "id_tipo_documento"=>"1",
            "id_departamento"=>"27",
            "id_municipio"=>"866",
            "genero"=>"Masculino",
            "celular"=>"3000000000",
            "id_autentication"=> "1",
        ]); 
    }
}
