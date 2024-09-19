<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;


class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

                // Crear el enlace simbólico de storage
                Artisan::call('storage:link');

                // Crear la carpeta 'banners' en el directorio de almacenamiento público
                $bannersPath = storage_path('app/public/banners');
                if (!File::exists($bannersPath)) {
                    File::makeDirectory($bannersPath, 0755, true);
                }
        
                // Ruta de la imagen de origen y destino
                $sourceImagePath = base_path('resources/imagen/1_1@300x-100 (1).webp');
                $destinationImageName = '5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.webp';
                $destinationImagePath = $bannersPath . '/' . $destinationImageName;
        
                $sourceImagePaths = base_path('resources/imagen/2_1@300x-100.webp');
                $destinationImageNames = '5bNMib9x9pD058TepwVBgAdddF1kNW5OzNULndSD.webp';
                $destinationImagePaths = $bannersPath . '/' . $destinationImageNames;
        
                // Copiar la imagen a la carpeta 'banners'
                if (File::exists($sourceImagePath)) {
                    File::copy($sourceImagePath, $destinationImagePath);
                    $this->command->info('The image has been copied to the banners folder successfully!');
                } else {
                    $this->command->error('The source image does not exist.');
                }
        
                if (File::exists($sourceImagePaths)) {
                    File::copy($sourceImagePaths, $destinationImagePaths);
                    $this->command->info('The image has been copied to the banners folder successfully!');
                } else {
                    $this->command->error('The source image does not exist.');
                }
        
                // URL de la imagen para guardar en la base de datos
                $bannerUrl = 'storage/banners/' . $destinationImageName;
                $bannerUrl2 = 'storage/banners/' . $destinationImageNames;


        Banner::create([
            "urlImagen" => $bannerUrl,
            "estadobanner" => 1,
            "id_aliado" => 1
        ]);

        Banner::create([
            "urlImagen" => $bannerUrl2,
            "estadobanner" => 1,
            "id_aliado" => 1
        ]);
    }
}
