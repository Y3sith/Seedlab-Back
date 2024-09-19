<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aliado;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


class AliadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el enlace simbólico de storage
        Artisan::call('storage:link');

        // Crear la carpeta 'logos' en el directorio de almacenamiento público
        $logosPath = storage_path('app/public/logos');
        if (!File::exists($logosPath)) {
            File::makeDirectory($logosPath, 0755, true);
        }

        $documentosPath = storage_path('app/public/documentos');
        if (!File::exists($documentosPath)) {
            File::makeDirectory($documentosPath, 0755, true);
        }

        $multimediaPath = storage_path('app/public/imagenes');
                if (!File::exists($multimediaPath)) {
                    File::makeDirectory($multimediaPath, 0755, true);
                }
        
                // Ruta de la imagen de origen y destino
                $sourceImagePath = base_path('resources/imagen/Inpulsa_home.webp');
                $destinationImageName = '5bNMib9x9pD058TepwVBgA2JdF1kNW5OzNUL.webp';
                $destinationImagePath = $multimediaPath . '/' . $destinationImageName;
        
                $sourceImagePaths = base_path('resources/imagen/tecnoparque.webp');
                $destinationImageNames = '5bNMib9x9pD058TepwVBgAdddF1k.webp';
                $destinationImagePaths = $multimediaPath . '/' . $destinationImageNames;
        
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
                $inpulsaimagen = 'storage/imagenes/' . $destinationImageName;
                $tecnoparqueimagen = 'storage/imagenes/' . $destinationImageNames;


        $aliados = [
            [
                "nombre" => "Camara de comercio",
                "descripcion" => "La Cámara de Comercio de Bucaramanga trabaja por el desarrollo socioeconómico de la región mediante el fortalecimiento de la competitividad empresarial, regional y la prestación eficiente de los servicios delegados por el estado.",
                "logo" => "camara_de_comercio.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=bS48a8OypPw&pp=ygUiY2FtYXJhIGRlIGNvbWVyY2lvIGRlIGJ1Y2FyYW1hbmdhIA%3D%3D",
                "id_autentication" => 8,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://www.camaradirecta.com/"
            ],
            [
                "nombre" => "Ucc",
                "descripcion" => "Somos una Institución multicampus de propiedad social, educamos personas con las competencias para responder a las dinámicas del mundo, contribuimos a la construcción y difusión del conocimiento, apoyamos el desarrollo competitivo del país a través de sus organizaciones y buscamos el mejoramiento de la calidad de vida de las comunidades.",
                "logo" => "ucc.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=tXoIVjVuYDQ&pp=ygUTdWNjIGRlIGJ1Y2FyYW1hbmdhIA%3D%3D",
                "id_autentication" => 11,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://www.ucc.edu.co/"
            ],
            [
                "nombre" => "Ecopetrol",
                "descripcion" => "Ecopetrol S.A. es una Compañía organizada bajo la forma de sociedad anónima, del orden nacional, vinculada al Ministerio de Minas y Energía. Tiene operaciones ubicadas en el centro, sur, oriente y norte de Colombia, al igual que en el exterior.",
                "logo" => "ecopetrol.webp",
                "ruta_multi" => "https://youtu.be/1HGqT3SR8gc?si=PMXpBZ7WbmIbaHWQ",
                "id_autentication" => 6,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://www.ecopetrol.com.co/wps/portal"
            ],
            [
                "nombre" => "Imebu",
                "descripcion" => "El Instituto Municipal de Empleo y Fomento Empresarial del Municipio de Bucaramanga es un establecimiento público de orden municipal, dotado de personería jurídica, autonomía administrativa y financiera, con patrimonio independiente.",
                "logo" => "imebu.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=qy2RG_rrGtQ",
                "id_autentication" => 7,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://imebu.gov.co/web5/"
            ],
            [
                "nombre" => "Otri",
                "descripcion" => "La Oficina de Transferencia de Resultados de Investigación Estratégica de Oriente está orientada a fortalecer las capacidades institucionales de manera sostenible para impulsar efectivamente la transferencia tecnológica hacia las empresas y la sociedad desde generadores y creadores de conocimiento y tecnología.",
                "logo" => "otri.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=Ru-DzxPcBPc&pp=ygUQb3RyaSBidWNhcmFtYW5nYQ%3D%3D",
                "id_autentication" => 9,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://www.otrieo.com/conocenos/otri/index.php"
            ],
            [
                "nombre" => "Unab",
                "descripcion" => "La Universidad Autónoma de Bucaramanga se encuentra ubicada en la ciudad de Bucaramanga capital del Departamento de Santander Colombia. Tiene un área construida total de 32.83 Ha, distribuidas en hermosos campus, espacios de práctica y servicio social.",
                "logo" => "unab.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=czUlii8WWDQ&pp=ygUQdWJhbiBidWNhcmFtYW5nYQ%3D%3D",
                "id_autentication" => 10,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://unab.edu.co/"
            ],
            [
                "nombre" => "C-emprende",
                "descripcion" => "La estrategia regional CEmprende es el eje articulador y potenciador en los territorios de la política de reindustrialización, bajo los lineamientos del Ministerio de Comercio, Industria y Turismo.",
                "logo" => "c-emprende.webp",
                "ruta_multi" => "https://www.youtube.com/watch?v=_ltfgiv4RVw&pp=ygUKYyBlbXByZW5kZQ%3D%3D",
                "id_autentication" => 12,
                "id_tipo_dato" => 1,
                "urlpagina" => "https://www.innpulsacolombia.com/nuestros-programas/cemprende/"
            ],
            [
                "nombre" => "Tecnoparque",
                "descripcion" => "Es un programa de innovación tecnológica del Servicio Nacional de Aprendizaje dirigida a todos los Colombianos, que actúa como acelerador para el desarrollo de proyectos materializados en prototipos funcionales en cuatro líneas tecnológicas: Electrónica y Telecomunicaciones, Tecnologías Virtuales, Ingeniería y diseño y Biotecnología nanotecnología.",
                "logo" => "tecnoparque.webp",
                "ruta_multi" => $tecnoparqueimagen,
                "id_autentication" => 13,
                "id_tipo_dato" => 2,
                "urlpagina" => "https://www.sena.edu.co/es-co/formacion/Paginas/tecnoparques.aspx"

            ],
            [
                "nombre" => "Innpulsa colombia",
                "descripcion" => "Iniciativa que promueve mejores prácticas empresariales y el desarrollo de las personas para el fortalecimiento y la sostenibilidad de micro y pequeños negocios y unidades productivas de la economía popular.",
                "logo" => "innpulsa.webp",
                "ruta_multi" => $inpulsaimagen,
                "id_autentication" => 14,
                "id_tipo_dato" => 2,
                "urlpagina" => "https://www.innpulsacolombia.com/"
            ],

        ];

        foreach($aliados as $aliadoData){
            if (!empty($aliadoData['logo'])) {
                $sourcePath = resource_path('imagen/' . $aliadoData['logo']);
                $destinationPath = $logosPath . '/' . $aliadoData['logo'];

                if (File::exists($sourcePath) && !File::exists($destinationPath)) {
                    File::copy($sourcePath, $destinationPath);
                }
            }
        }

        foreach ($aliados as $aliadoData) {
            if (!empty($aliadoData['logo'])) {
                $aliadoData['logo'] = 'logos/' . $aliadoData['logo'];
            }
            Aliado::create($aliadoData);
        }
    }
}
