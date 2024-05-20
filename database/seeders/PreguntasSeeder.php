<?php

namespace Database\Seeders;

use App\Models\Preguntas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntas = [
            
            '1' => [
                '1' => ['nombre' => '¿Cuantós personas conforman su equipo de trabajo?','puntaje' => '0'],
                '2' => ['nombre' => 'Cuenta con personas de apoyo en:','puntaje' => '0'],
                '3' => ['nombre' => '¿Su emprendimiento esta legalmente constituido?','puntaje' => '2'],
                '4' =>['nombre' => '¿Cumple con las normas tributarias, contables, laborales, comerciales y/o legales para desempeñar la actividad?','puntaje' => '2'],
                '5' =>['nombre' => '¿Tiene claramente definido su modelo de negocio?','puntaje' => '2'],
                '6' =>['nombre' => '¿Tiene claramente definido su plan de negocios?','puntaje' => '2'],
                '7' =>['nombre' => '¿Tiene experiencia comercial relacionada con el producto y/o servicio?','puntaje' => '2'],
                '8' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuánto tiempo de experiencia relacionada tiene?','puntaje' => '2'],
                '9' =>['nombre' => '¿Su emprendimiento tiene definido la misión y la visión?','puntaje' => '2'],
                '10' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar ¿Cuál es su misión?','puntaje' => '0'],
                '11' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar ¿Cuál es su visión?','puntaje' => '0'],
                '12' =>['nombre' => '¿Tiene definidas metas empresariales?','puntaje' => '2'],
                '13' =>['nombre' => '¿El personal de apoyo está debidamente contratado?','puntaje' => '2'],
                '14' =>['nombre' => '¿Los cargos de apoyo tienen funciones claramente definidas?','puntaje' => '2'],
                '15' =>['nombre' => '¿El perfil de los apoyos está debidamente definido?','puntaje' => '2'],

            ],
            '2' => [
                '16' => ['nombre' => '¿Tiene identificado los gastos y costos de su emprendimiento?','puntaje' => '2'],
                '17' => ['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?','puntaje' => '0'],
                '18' => ['nombre' => '¿En su emprendimiento elabora estados financieros?','puntaje' => '2'],
                '19' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?','puntaje' => '0'],
                '20' =>['nombre' => '¿Tiene claridad sobre qué presupuestos debe elaborar para su emprendimiento?','puntaje' => '2'],
                '21' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?','puntaje' => '0'],
                '22' =>['nombre' => '¿Los costos de su producto y/o servicio están claramente definidos?','puntaje' => '2'],
                '23' =>['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Qué factores tiene en cuenta para definir el precio de su producto y/o servicio?','puntaje' => '0'],
                '24' =>['nombre' => '¿Cuáles alternativas de financiamiento usa para apoyar su emprendimiento?','puntaje' => '0'],
                '25' =>['nombre' => '¿Su producto y/o servicio presenta en la actualidad ventas?','puntaje' => '2'],
                '26' =>['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuál es el valor promedio / estimado de las ventas al año?','puntaje' => '0'],
                '27' =>['nombre' => '¿Cuáles canales de ventas usa para comercializar su negocio?','puntaje' => '0'],
                '28' =>['nombre' =>  '¿Sabe cuáles obligaciones aplican a su emprendimiento?','puntaje' => '2'],
                '29' =>['nombre' =>  'Si la anterior respuesta fue afirmativa: ¿Cuáles?','puntaje' => '0'],
            
            ],
            '3'=>[
                '30' => ['nombre' => '¿Tiene claramente definido sus clientes actuales?','puntaje' => '2'],
                '31' => ['nombre' => 'Si tiene clientes actuales: ¿Quiénes son?','puntaje' => '2'],
                '32' => ['nombre' => '¿Tiene definido sus clientes potenciales?','puntaje' => '2'],
                '33' =>['nombre' => 'Si tiene definido sus clientes potenciales: ¿Quiénes son?','puntaje' => '2'],
                '34' =>['nombre' => '¿Tiene definido los competidores de su producto y/o servicio?','puntaje' => '2'],
                '35' =>['nombre' =>  '¿Le gustaría ser cómo?','puntaje' => '2'],
                '36' =>['nombre' => '¿No te gustaría ser cómo?','puntaje' => '2'],
                '37' =>['nombre' => '¿Tiene identificado con claridad el factor diferencial de su empresa, producto y/o servicio?','puntaje' => '2'],
                '38' =>['nombre' => '¿Ha participado en otras estrategias de fortalecimiento, semilla, aceleración y/o similares?','puntaje' => '2'],
                '39' =>['nombre' => '¿Está dispuesto a realizar alianzas para la venta y/o distribución de sus productos, así como para la adquisición de equipos, insumos y/o materiales?','puntaje' => '2'],
                '40' =>['nombre' => '¿Está dispuesto a realizar alianzas y/o convenios para el uso de equipos con otras instituciones y/u organizaciones para el desarrollo de productos?','puntaje' => '2'],
                '41' =>['nombre' => '¿Está dispuesto a realizar alianza y/o convenios para recibir apoyo técnico especializado para el desarrollo de productos y/o servicios?','puntaje' => '2'],

            ],
            '4'=>[
                '42' => ['nombre' => 'Deficición de TRL','puntaje' => '0'],
                '43' => ['nombre' => '¿Cuenta con área o departamento de innovación y/o desarrollo tecnológico?','puntaje' => '2'],
                '44' => ['nombre' => '¿Tiene definidas las necesidades de su producto y/o servicio?','puntaje' => '2'],
                '45' => ['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuáles?','puntaje' => '0'],
                '46' => ['nombre' => '¿Tiene definidas las necesidades de su emprendimiento (organizacional)?','puntaje' => '0'],
                '47' => ['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuáles?','puntaje' => '0'],
            ]

        ];

        foreach ($preguntas as $idSeccion => $preguntasSeccion) {
            foreach ($preguntasSeccion as $idPregunta => $pregunta) {
                Preguntas::create([
                    'nombre' => $pregunta['nombre'],
                    'puntaje' => $pregunta['puntaje'],
                    'id_seccion' => $idSeccion
                ]);
            }
        }
        
        
    }
}
