<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orientador;

class OrientadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Orientador::create([
            "nombre"=> "David",
            "apellido"=> "Hernandez",
            "documento"=> "213456",
            "imagen_perfil"=>"",
            "fecha_nac"=>"2024-05-21",
            "id_tipo_documento"=>"1",
            "id_municipio"=>"866",
            //"email"=>"",
            "direccion"=>"N/A",
            "genero"=>"N/A",
            "celular"=> "N/A",
            "id_autentication"=> "2",
        ]); 
    }
}
