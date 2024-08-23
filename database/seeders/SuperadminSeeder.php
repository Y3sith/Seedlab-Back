<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\File;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                
        SuperAdmin::create([
            "nombre"=> "Esneider",
            "apellido"=> "Jerez",
            "documento"=> "213456",
            "direccion"=>"Cra 28 # 39-06",
            "fecha_nac"=>"2024-05-21",
            "id_tipo_documento"=>"1",
            "id_municipio"=>"866",
            "genero"=>"Masculino",
            "celular"=>"320147941",
            "imagen_perfil"=>$bannerUrl2,
            "id_autentication"=> "1",
        ]); 
    }
}
