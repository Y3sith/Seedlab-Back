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
        $logosPath = storage_path('app/public/logos');

        $sourceImagePath = base_path('resources/imagen/logoSeed.png');
                $destinationImageName = '5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.jpg';
                $destinationImagePath = $logosPath . '/' . $destinationImageName;
        
                $sourceImagePaths = base_path('resources/imagen/logoSeed blanco.png');
                $destinationImageNames = '5bNMib9x9pD058TepwVBgAdddF1kNW5OzNULndSD.jpg';
                $destinationImagePaths = $logosPath . '/' . $destinationImageNames;
        
                // Copiar la imagen a la carpeta 'banners'
                if (File::exists($sourceImagePath)) {
                    File::copy($sourceImagePath, $destinationImagePath);
                    $this->command->info('The image');
                } else {
                    $this->command->error('The source image does not exist.');
                }
        
                if (File::exists($sourceImagePaths)) {
                    File::copy($sourceImagePaths, $destinationImagePaths);
                    $this->command->info('The image');
                } else {
                    $this->command->error('The source image does not exist.');
                }
        
                // URL de la imagen para guardar en la base de datos
                $logoUrl = 'storage/logos/' . $destinationImageName;
                $logoUrl2 = 'storage/logos/' . $destinationImageNames;

        PersonalizacionSistema::create([
            'imagen_logo' =>  $logoUrl,
            'nombre_sistema' => 'SeedLab',
            'color_principal' => '#00B3ED',
            'color_secundario' => '#FA7D00',
            'color_terciario' => '#fff',
            'logo_footer' => $logoUrl2,
            'descripcion_footer' => 'Este programa estará enfocado en emprendimientos de base tecnológica, para ideas validadas, que cuenten con un codesarrollo, prototipado y pruebas de concepto. Se va a abordar en temas como Big Data, ciberseguridad e IA, herramientas de hardware y software, inteligencia competitiva, vigilancia tecnológica y propiedad intelectual.',
            'paginaWeb' => 'seedlab.com',
            'email' => 'email@seedlab.com',
            'telefono' => '(55) 5555-5555',
            'direccion' => 'Calle 48 # 28 - 40',
            'ubicacion' => 'Bucaramanga, Santander, Colombia',
            'id_superadmin' => 1,
        ]);
    }
}
