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
            ['nombre'=>'DATOS DE LOS EMPRENDEDORES E INFORMACIÓN GENERAL','puntaje'=>0],
            ['nombre'=>'INFORMACIÓN FINANCIERA','puntaje'=>0],
            ['nombre'=> 'INFORMACIÓN DEL MERCADO','puntaje'=>0],
            ['nombre'=>'INFORMACIÓN OPERATIVA TÉCNICA DE PRODUCTO Y/O SERVICIO','puntaje'=>0],
        ];

        foreach ($seccionPorPregunta as $seccion) {
            Seccion::create([
                'nombre' => $seccion['nombre'],
                'puntaje' => $seccion['puntaje'],
            ]);
        }
    }
}
