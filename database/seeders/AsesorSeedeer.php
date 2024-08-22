<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asesor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AsesorSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('storage:link'); 

        $fotoPerfilPath = storage_path('app/public/fotoPerfil');
        if (!File::exists($fotoPerfilPath)) {
            File::makeDirectory($fotoPerfilPath, 0755, true);
        }

        $sourceImagePaths = base_path('resources/imagen/usuario.jpg');
                $destinationImageNames = '5bNMib9x9pD058TepwVBgAdddF1kNW5OzNULndSD.jpg';
                $destinationImagePaths = $fotoPerfilPath . '/' . $destinationImageNames;
       
                // Copiar la imagen a la carpeta 'banners
                if (File::exists($sourceImagePaths)) {
                    File::copy($sourceImagePaths, $destinationImagePaths);
                    $this->command->info('The image has been copied to the banners folder successfully!');
                } else {
                    $this->command->error('The source image does not exist.');
                }
       
                // URL de la imagen para guardar en la base de datos
                $bannerUrl2 = 'storage/fotoPerfil/' . $destinationImageNames;

        Asesor::create([
            "id" => "1",
            "nombre" => "Juan",
            "apellido" => "Perez",
            "documento" => "N/A",
            "imagen_perfil" => $bannerUrl2,
            "fecha_nac" => "2024-05-21",
            "id_aliado" => "1",
            "id_tipo_documento" => "1",
            "id_municipio" => "866",
            //"email"=>"",
            "direccion" => "N/A",
            "genero" => "N/A",
            "celular" => "N/A",
            "id_autentication" => "4",
            //"id_aliado"=> "1",
        ]);
    }
}
