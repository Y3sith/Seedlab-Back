<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asesor;

class AsesorSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Asesor::create([
            "id"=> "1",
            "nombre"=> "Juan",
            "apellido"=> "Perez",
            "documento"=>"N/A",
            "imagen_perfil"=>"",
            "fecha_nac"=>"2024-05-21",
            "id_aliado"=>"1",
            "id_tipo_documento"=>"1",
            "id_municipio"=>"866",
            //"email"=>"",
            "direccion"=>"N/A",
            "genero"=>"N/A",
            "celular" => "N/A",
            "id_autentication"=> "4",
            //"id_aliado"=> "1",
        ]);
    }
}
