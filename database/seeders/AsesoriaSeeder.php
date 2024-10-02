<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asesoria;

class AsesoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Asesoria::create([
            "id" => "1",
            "Nombre_sol" => "Creacion de semilla",
            "notas" => "Quiero hacer una semilla de asesoria",
            "isorientador" => false,
            "asignacion" => false,
            "fecha" => "2024-05-21 14:30:00",
            "id_aliado" => "1",
            "doc_emprendedor" => "0000000001",
        ]);
    
        Asesoria::create([
            "id" => "2",
            "Nombre_sol" => "eliminar de semilla",
            "notas" => "Quiero eliminar una semilla de asesoria",
            "isorientador" => false,
            "asignacion" => false,
            "fecha" => "2024-05-22 14:30:00",
            "id_aliado" => "2",
            "doc_emprendedor" => "0000000001",
        ]);

        Asesoria::create([
            "id" => "3",
            "Nombre_sol" => "Edicion de semilla",
            "notas" => "Quiero editar una semilla de asesoria",
            "isorientador" => false,
            "asignacion" => false,
            "fecha" => "2024-05-23 14:30:00",
            "id_aliado" => "1",
            "doc_emprendedor" => "0000000001",
        ]);

        Asesoria::create([
            "id" => "4",
            "Nombre_sol" => "listado de semilla",
            "notas" => "Quiero listar una semilla de asesoria",
            "isorientador" => true,
            "asignacion" => false,
            "fecha" => "2024-05-24 14:30:00",
            "id_aliado" => null,
            "doc_emprendedor" => "0000000001",
        ]);

        Asesoria::create([
            "id" => "5",
            "Nombre_sol" => "Actividad asesoria #2",
            "notas" => "Quiero una asesoria personalizada",
            "isorientador" => true,
            "asignacion" => true,
            "fecha" => "2024-05-24 14:30:00",
            "id_aliado" => "3",
            "doc_emprendedor" => "0000000001",
        ]);
    }
}