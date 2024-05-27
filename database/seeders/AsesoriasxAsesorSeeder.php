<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AsesoriaxAsesor;


class AsesoriasxAsesorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AsesoriaxAsesor::create([
            'id_asesor' => 1,
            'id_asesoria' => 5,
        ]);
        AsesoriaxAsesor::create([
            'id_asesor' => 1,
            'id_asesoria' => 6,
        ]);
    }
}
