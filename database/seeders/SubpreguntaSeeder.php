<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subpreguntas;

class SubpreguntaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntasPorSubpreguntas = [
            '2' => [
                '1' => ['texto' => 'Administrativo',
                        'puntaje' => '2'],
                '2' => ['texto' => 'Desarrollo',
                'puntaje' => '2'],
                '3' => ['texto' => 'Producción',
                'puntaje' => '2'],
                '4' => ['texto' => 'Innovacion y/o desarrollo',
                        'puntaje' => '2'],
                '5' => ['texto' => 'Comercialización',
                        'puntaje' => '2'],
                '6' => ['texto' => 'Otro, cuál / cuántos?',
                'puntaje' => '2'],
            ],
            '17'=>[
                '7'=>['texto' => 'Gastos fijos',
                    'puntaje' => '2'],
                '8'=>['texto' => 'Gastos variables',
                    'puntaje' => '2'],
                '9'=>['texto' => 'Gastos operacionales',
                    'puntaje' => '2'],
                '10'=>['texto' => 'Gastos no operacionales',
                    'puntaje' => '2'],
                '11'=>['texto' => 'Costos fijos',
                    'puntaje' => '2'],
                '12'=>['texto' => 'Costos variables',
                    'puntaje' => '2'],
                '13'=>['texto' => 'Costos directos',
                    'puntaje' => '2'],
                '14'=>['texto' => 'Costos indirectos',
                'puntaje' => '2'],
            ],
            '19'=>[
                '15'=>['texto' => 'Balance general',
                    'puntaje' => '2'],
                '16'=>['texto' => 'Estado de flujo',
                    'puntaje' => '2'],
                '17'=>['texto' => 'Registro de compras',
                    'puntaje' => '2'],
                '18'=>['texto' => 'Registro de ventas',
                    'puntaje' => '2'],
            ],
            '21'=>[
                '19'=>['texto' => 'Ingreso',
                    'puntaje' => '2'],
                '20'=>['texto' => 'Egreso',
                    'puntaje' => '2'],
                '21'=>['texto' => 'Deudas',
                    'puntaje' => '2'],
            ],
            '23'=>[
                '22'=>['texto' => 'Costos',
                    'puntaje' => '2'],
                '23'=>['texto' => 'Demanda',
                    'puntaje' => '2'],
                '24'=>['texto' => 'Competencia',
                    'puntaje' => '2'],
            ],
            '24'=>[
                '25'=>['texto' => 'Prestamo formal',
                    'puntaje' => '2'],
                '26'=>['texto' => 'Prestamo informal',
                    'puntaje' => '2'],
                '27'=>['texto' => 'Disminuyendo gastos',
                    'puntaje' => '2'],
                '28'=>['texto' => 'Ahorros/propios',
                    'puntaje' => '2'],
            ],
            '27'=>[
                '29'=>['texto' => 'Punto de venta',
                    'puntaje' => '2'],
                '30'=>['texto' => 'Telemarketing',
                    'puntaje' => '2'],
                '31'=>['texto' => 'Marketplace',
                    'puntaje' => '2'],
                '32'=>['texto' => 'Ecommerce',
                    'puntaje' => '2'],
            ],
            '29'=>[
                '33'=>['texto' => 'Iva',
                    'puntaje' => '2'],
                '34'=>['texto' => 'Ica',
                    'puntaje' => '2'],
                '35'=>['texto' => 'Retefuente',
                    'puntaje' => '2'],
                '36'=>['texto' => 'Impuesto a la renta',
                    'puntaje' => '2'],
            ],
            '45'=>[
                '37'=>['texto' => 'Apoyo tecnico',
                    'puntaje' => '2'],
                '38'=>['texto' => 'Capacitación',
                    'puntaje' => '2'],
                '39'=>['texto' => 'Financiamiento',
                    'puntaje' => '2'],
                '40'=>['texto' => 'Redes/alianza',
                    'puntaje' => '2'],
                '41'=>['texto' => 'Mejora de la calidad p/s',
                    'puntaje' => '2'],
                '42'=>['texto' => 'Infraestructura',
                    'puntaje' => '2'],
            ],
            '47'=>[
                '43'=>['texto' => 'Apoyo tecnico',
                    'puntaje' => '2'],
                '44'=>['texto' => 'Capacitación',
                    'puntaje' => '2'],
                '45'=>['texto' => 'Financiamiento',
                    'puntaje' => '2'],
                '46'=>['texto' => 'Redes/alianza',
                    'puntaje' => '2'],
                '47'=>['texto' => 'Infraestructura',
                    'puntaje' => '2'],
                '48'=>['texto' => 'Aumento de clientes',
                    'puntaje' => '2'],
                '49'=>['texto' => 'Bajar costos y/o gastos',
                    'puntaje' => '2'],
                '50'=>['texto' => 'Mejorar ventas',
                    'puntaje' => '2'],

            ]
        ];

        foreach($preguntasPorSubpreguntas as $idPregunta =>$preguntas ){
            foreach($preguntas as $nombrePregunta =>$contenido){
                Subpreguntas::create([
                    'texto' =>$contenido['texto'],
                    'puntaje' =>$contenido['puntaje'],
                    'id_pregunta' => $idPregunta
                ]);
            }
        }
    }
}
