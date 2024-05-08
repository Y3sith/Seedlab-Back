<?php

namespace Database\Seeders;

use App\Models\Subpreguntas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubpreguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntasPorSubpreguntas = [
            1.1 => [null],
            1.2 => ['Administrativo', 'Desarrollo', 'Produción', 'Innovación y/o desarrollo tecnológico', 'Comercialización', 'Otro, cuál / cuántos?'],
            1.3 => [null],
            1.4 => [null],
            1.5 => [null],
            1.7 => [null],
            1.8 => [null],
            1.9 => [null],
            1.10 => [null],
            1.10 => [null],
            1.11 => [null],
            1.12 => [null],
            1.13 => [null],
            1.14 => [null],

            2.1 => [null],
            2.2 => ['GASTOS FIJOS', 'GASTOS VARIABLES', 'GASTOS OPERACIONALES', 'GASTOS NO OPERACIONALES', 'GASTOS FIJOS', 'COSTOS VARIABLES', 'COSTOS DIRECTOS', 'COSTOS INDIRECTOS'],
            2.3 => [null],
            2.4 => ['BALANCE GENERAL', 'ESTADO DE FLUJO', 'REGISTRO DE COMPRAS', 'REGISTRO DE VENTAS', null],
            2.5 => [null],
            2.6 => ['INGRESO', 'EGRESO', 'DEUDAS', null],
            2.10 => [null],
            2.11 => ['COSTOS', 'DEMANDA', 'COMPETENCIA', null],
            2.12 => ['PRESTAMO FORMAL', 'PRESTAMO INFORMAL', 'DISMINUYENDO GASTOS', 'AHORROS/PROPIOS', null],
            2.13 => [null],
            2.14 => [null],
            2.15 => ['PUNTO DE VENTA', 'TELEMARKETING', 'MARKETPLACE', 'ECOMMERCE', null],
            2.16 => [null],
            2.17 => ['IVA', 'ICA', 'RETEFUENTE', 'IMPUESTO A LA RENTA'],

            3.1 => [null],
            3.2 => [null],
            3.3 => [null],
            3.4 => [null],
            3.5 => [null],
            3.6 => [null],
            3.7 => [null],
            3.8 => [null],
            3.9 => [null],
            3.10 => [null],
            3.11 => [null],
            3.12 => [null],

            4.1 => [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            4.2 => [null],
            4.3 => [null],
            4.4 => ['APOYO TÉCNICO', 'Capacitación', 'FINANCIAMIENTO', 'REDES/ALIANZA', 'MEJORA DE LA CALIDAD P/S', 'INFRAESTRUCTURA', null],
            4.5 => [null],
            4.6 => ['APOYO TÉCNICO', 'Capacitación', 'FINANCIAMIENTO', 'REDES/ALIANZA', 'INFRAESTRUCTURA', 'AUMENTO DE CLIENTES', 'BAJAR COSTOSY/O GASTOS', 'MEJORAR BENTAS', null]
        ];
        foreach($preguntasPorSubpreguntas as $idPregunta =>$preguntas ){
            foreach($preguntas as $nombrePregunta){
                Subpreguntas::create([
                    'nombrePregunta' =>$nombrePregunta,
                    'idPregunta' =>$idPregunta
                ]);
            }
        }
    }
}
