<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;


class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuperAdmin::create([
            "nombre"=> "Esneider",
            "apellido"=> "Jerez",
            "direccion"=>"N/A",
            "fecha_nac"=>"2024-05-21",
            "id_tipo_documento"=>"1",
            "id_municipio"=>"866",
            "genero"=>"N/A",
            "celular"=>"",
            "imagen_perfil"=>"N/A",
            "id_autentication"=> "1",
        ]); 
    }
}
