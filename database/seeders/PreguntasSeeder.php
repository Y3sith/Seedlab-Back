<?php

namespace Database\Seeders;

use App\Models\Preguntas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntas = [
            
            '1' => [
                '1' => ['nombre' => '¿Cuantós personas conforman su equipo de trabajo?'],
                '2' => ['nombre' => 'CUENTA CON PERSONAS DE APOYO EN:'],
                '3' => ['nombre' => '¿SU EMPRENDIMIENTO ESTÁ LEGALMENTE CONSTITUIDO?'],
            ],
        ];

        foreach ($preguntas as $idSeccion => $preguntasSeccion) {
            foreach ($preguntasSeccion as $idPregunta => $pregunta) {
                Preguntas::create([
                    'nombre' => $pregunta['nombre'],
                    'id_seccion' => $idSeccion
                ]);
            }
        }
        
        
    }
}
