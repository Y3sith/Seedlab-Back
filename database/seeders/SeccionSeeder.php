<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seccion;

class SeccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seccionPorPregunta=[
            'DATOS DE LOS EMPRENDEDORES E INFORMACIÓN GENERAL',
            'INFORMACIÓN FINANCIERA',
            'INFORMACIÓN DEL MERCADO',
            'INFORMACIÓN OPERATIVA TÉCNICA DE PRODUCTO Y/O SERVICIO',
        ];

            foreach($seccionPorPregunta as $nombrePregunta){
                Seccion::create([
                    'nombre' => $nombrePregunta,
                ]);
            }
    }
}
