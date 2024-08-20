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
            "direccion"=>"",
            "genero"=>"",
            "celular"=>"",
            "imagen_perfil"=>"",
            "id_autentication"=> "1",
        ]); 
    }
}
