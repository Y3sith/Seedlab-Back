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
            ['nombre'=>'DATOS DE LOS EMPRENDEDORES E INFORMACIÓN GENERAL'],
            ['nombre'=>'INFORMACIÓN FINANCIERA'],
            ['nombre'=> 'INFORMACIÓN DEL MERCADO'],
            ['nombre'=> 'TRL'],
            ['nombre'=>'INFORMACIÓN OPERATIVA TÉCNICA DE PRODUCTO Y/O SERVICIO'],
        ];

        foreach ($seccionPorPregunta as $seccion) {
            Seccion::create([
                'nombre' => $seccion['nombre'],
                // 'puntaje' => $seccion['puntaje'],
            ]);
        }
    }
}
