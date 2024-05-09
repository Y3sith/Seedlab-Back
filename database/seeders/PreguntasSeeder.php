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
                '1' => ['nombre' => '¿Cuantós personas conforman su equipo de trabajo?'],
                '2' => ['nombre' => 'Cuenta con personas de apoyo en:'],
                '3' => ['nombre' => '¿Su emprendimiento esta legalmente constituido?'],
                '4' =>['nombre' => '¿Cumple con las normas tributarias, contables, laborales, comerciales y/o legales para desempeñar la actividad?'],
                '5' =>['nombre' => '¿Tiene claramente definido su modelo de negocio?'],
                '6' =>['nombre' => '¿Tiene claramente definido su plan de negocios?'],
                '7' =>['nombre' => '¿Tiene experiencia comercial relacionada con el producto y/o servicio?'],
                '8' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuánto tiempo de experiencia relacionada tiene?'],
                '9' =>['nombre' => '¿Su emprendimiento tiene definido la misión y la visión?'],
                '10' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar ¿Cuál es su misión?'],
                '11' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar ¿Cuál es su visión?'],
                '12' =>['nombre' => '¿Tiene definidas metas empresariales?'],
                '13' =>['nombre' => '¿El personal de apoyo está debidamente contratado?'],
                '14' =>['nombre' => '¿Los cargos de apoyo tienen funciones claramente definidas?'],
                '15' =>['nombre' => '¿El perfil de los apoyos está debidamente definido?'],

            ],
            '2' => [
                '16' => ['nombre' => '¿Tiene identificado los gastos y costos de su emprendimiento?'],
                '17' => ['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?'],
                '18' => ['nombre' => '¿En su emprendimiento elabora estados financieros?'],
                '19' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?'],
                '20' =>['nombre' => '¿Tiene claridad sobre qué presupuestos debe elaborar para su emprendimiento?'],
                '21' =>['nombre' => 'Si la respuesta anterior fue afirmativa indicar: ¿Cuáles?'],
                '22' =>['nombre' => '¿Los costos de su producto y/o servicio están claramente definidos?'],
                '23' =>['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Qué factores tiene en cuenta para definir el precio de su producto y/o servicio?'],
                '24' =>['nombre' => '¿Cuáles alternativas de financiamiento usa para apoyar su emprendimiento?'],
                '25' =>['nombre' => '¿Su producto y/o servicio presenta en la actualidad ventas?'],
                '26' =>['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuál es el valor promedio / estimado de las ventas al año?'],
                '27' =>['nombre' => '¿Cuáles canales de ventas usa para comercializar su negocio?'],
                '28' =>['nombre' =>  '¿Sabe cuáles obligaciones aplican a su emprendimiento?'],
                '29' =>['nombre' =>  'Si la anterior respuesta fue afirmativa: ¿Cuáles?'],
            
            ],
            '3'=>[
                '30' => ['nombre' => '¿Tiene claramente definido sus clientes actuales?'],
                '31' => ['nombre' => 'Si tiene clientes actuales: ¿Quiénes son?'],
                '32' => ['nombre' => '¿Tiene definido sus clientes potenciales?'],
                '33' =>['nombre' => 'Si tiene definido sus clientes potenciales: ¿Quiénes son?'],
                '34' =>['nombre' => '¿Tiene definido los competidores de su producto y/o servicio?'],
                '35' =>['nombre' =>  '¿Le gustaría ser cómo?'],
                '36' =>['nombre' => '¿No te gustaría ser cómo?'],
                '37' =>['nombre' => '¿Tiene identificado con claridad el factor diferencial de su empresa, producto y/o servicio?'],
                '38' =>['nombre' => '¿Ha participado en otras estrategias de fortalecimiento, semilla, aceleración y/o similares?'],
                '39' =>['nombre' => '¿Está dispuesto a realizar alianzas para la venta y/o distribución de sus productos, así como para la adquisición de equipos, insumos y/o materiales?'],
                '40' =>['nombre' => '¿Está dispuesto a realizar alianzas y/o convenios para el uso de equipos con otras instituciones y/u organizaciones para el desarrollo de productos?'],
                '41' =>['nombre' => '¿Está dispuesto a realizar alianza y/o convenios para recibir apoyo técnico especializado para el desarrollo de productos y/o servicios?'],

            ],
            '4'=>[
                '42' => ['nombre' => 'Deficición de TRL'],
                '43' => ['nombre' => '¿Cuenta con área o departamento de innovación y/o desarrollo tecnológico?'],
                '44' => ['nombre' => '¿Tiene definidas las necesidades de su producto y/o servicio?'],
                '45' => ['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuáles?'],
                '46' => ['nombre' => '¿Tiene definidas las necesidades de su emprendimiento (organizacional)?'],
                '47' => ['nombre' => 'Si la anterior respuesta fue afirmativa: ¿Cuáles?'],
            ]

        ];

        foreach ($preguntas as $idSeccion => $preguntasSeccion) {
            foreach ($preguntasSeccion as $idPregunta => $pregunta) {
                Preguntas::create([
                    'nombre' => $pregunta['nombre'],
                    'id_seccion' => $idSeccion
                ]);
            }
        }
        
        
    }
}
