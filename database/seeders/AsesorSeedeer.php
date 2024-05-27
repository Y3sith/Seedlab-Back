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
            "celular" => "3146587645",
            "id_autentication"=> "4",
            "id_aliado"=> "2",
        ]);
    }
}
