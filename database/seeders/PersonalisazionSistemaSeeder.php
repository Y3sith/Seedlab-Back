<?php

namespace Database\Seeders;

use App\Models\PersonalizacionSistema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class PersonalisazionSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('storage:link');

        $logoPath = storage_path('app/public/logos');
        if(!File::exists($logoPath)){
            File::makeDirectory($logoPath, 0755, true);
        }

        $sourceImagePath = base_path('resources/imagen/logoSeed.png');
        $destinationImageName = 'logoSeed.png';
        $destinationImagePath = $logoPath .'/'. $destinationImageName;

        File::copy($sourceImagePath, $destinationImagePath);

        $storedImagePath = 'logos/'.$destinationImageName;

        $baseUrlImage = 'logos/'.$destinationImageName;

        PersonalizacionSistema::create([
            'imagen_logo' => $baseUrlImage,
            'nombre_sistema' => 'SeedLab',
            'color_principal' => '#00B3ED',
            'color_secundario' => '#FA7D00',
            'color_terciario' => '#fff',
            'id_superadmin' => 1,
        ]);
    }
}
