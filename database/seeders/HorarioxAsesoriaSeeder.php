<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HorarioAsesoria;

class HorarioxAsesoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HorarioAsesoria::create([
            'id' => 1,
            'observaciones' => "No hay ninguna observacion hasta el momento",
            'fecha' => "2024-07-23 14:30:00",
            'estado' => "Pendiente",
            'id_asesoria' => 5,
        ]);
        
    }
}
