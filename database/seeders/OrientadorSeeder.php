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
            "imagen_perfil"=>"",
            //"email"=>"",
            "direccion"=>"",
            "genero"=>"",
            "celular"=> "3157683542",
            "id_autentication"=> "2",
        ]); 
    }
}
