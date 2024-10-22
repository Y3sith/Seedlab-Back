<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Banner;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;


class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear el enlace simbólico de storage si no existe
        if (!File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        // Crear la carpeta 'banners' en el directorio de almacenamiento público
        $bannersPath = storage_path('app/public/banners');
        if (!File::exists($bannersPath)) {
            File::makeDirectory($bannersPath, 0755, true);
        }

        // Lista de imágenes de origen y sus destinos
        $images = [
            [
                'source' => base_path('resources/imagen/1_1@300x-100 (1).webp'),
                'destination' => '5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNULndSD.webp',
                'id_aliado' => 1,
            ],
            [
                'source' => base_path('resources/imagen/2_1@300x-100.webp'),
                'destination' => '5bNMib9x9pD058TepwVBgAdddF1kNW5OzNULndSD.webp',
                'id_aliado' => 1,
            ],
        ];

        foreach ($images as $imageData) {
            $sourceImagePath = $imageData['source'];
            $destinationImageName = $imageData['destination'];

            if (File::exists($sourceImagePath)) {
                $destinationImagePath = $bannersPath . '/' . $destinationImageName;
                File::copy($sourceImagePath, $destinationImagePath);

                // Procesar la imagen para generar diferentes tamaños
                $bannerUrls = $this->procesarImagenSeeder($destinationImagePath, $destinationImageName, 'banners');

                // Guardar el banner en la base de datos
                Banner::create([
                    'urlImagenSmall' => $bannerUrls['small'],
                    'urlImagenMedium' => $bannerUrls['medium'],
                    'urlImagenLarge' => $bannerUrls['large'],
                    'estadobanner' => 1,
                    'id_aliado' => $imageData['id_aliado'],
                ]);
            } else {
                $this->command->error("La imagen de origen '{$sourceImagePath}' no existe.");
            }
        }
    }

    private function procesarImagenSeeder($imagePath, $imageName, $folder)
    {
        $sizes = ['small' => 800, 'medium' => 1600, 'large' => 2400];
        $img = Image::make($imagePath);

        $baseFilename = pathinfo($imageName, PATHINFO_FILENAME);
        $imageUrls = [];

        foreach ($sizes as $sizeName => $width) {
            $resizedImage = $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $resizedFilename = "{$baseFilename}_{$sizeName}.webp";
            $resizedImagePath = storage_path("app/public/{$folder}/{$resizedFilename}");
            $resizedImage->encode('webp', 80)->save($resizedImagePath);

            $imageUrls[$sizeName] = "storage/{$folder}/{$resizedFilename}";
        }

        return $imageUrls;
    }
}
