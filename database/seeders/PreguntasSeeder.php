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
        $preguntas =[
    /*1.1*/'¿Cuantós personas conforman su equipo de trabajo?',
    /*1.2*/'CUENTA CON PERSONAS DE APOYO EN:',
    /*1.3*/'¿SU EMPRENDIMIENTO ESTÁ LEGALMENTE CONSTITUIDO?',
    /*1.4*/'¿CUMPLE CON LAS NORMAS TRIBUTARIAS, CONTABLES, LABORALES, COMERCIALES Y/O LEGALES PARA DESEMPEÑAR LA ACTIVIDAD?',
    /*1.5*/'¿TIENE CLARAMENTE DEFINIDO SU MODELO DE NEGOCIO?',
    /*1.6*/'¿TIENE CLARAMENTE DEFINIDO SU PLAN DE NEGOCIOS?',
    /*1.7*/'¿TIENE EXPERIENCIA COMERCIAL RELACIONADA CON EL PRODUCTO Y/O SERVICIO?',
    /*1.8*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR: ¿CUÁNTO TIEMPO DE EXPERIENCIA RELACIONADA TIENE?',       
    /*1.9*/'¿SU EMPRENDIMIENTO TIENE DEFINIDO LA MISIÓN Y LA VISIÓN?',
    /*1.10*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR ¿CUÁL ES SU MISIÓN?',
    /*1.10*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR ¿CUÁL ES SU VISIÓN?',
    /*1.12*/'¿TIENE DEFINIDA METAS EMPRESARIALES?',
    /*1.13*/'¿EL PERSONAL DE APOYO ESTÁ DEBIDAMENTE CONTRATADO?',
    /*1.14*/'¿LOS CARGOS DE APOYO TIENEN FUNCIONES CLARAMENTE DEFINIDAS?',
    /*1.15*/'¿EL PERFIL DE LOS APOYOS ESTÁ DEBIDAMENTE DEFINIDO?',

    /**2. INFORMACIÓN FINANCIERA */

    /*2.1*/'¿TIENE IDENTIFICADO LOS GASTOS Y COSTOS DE SU EMPRENDIMIENTO?',
    /*2.2*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR: ¿CUÁLES?',
    /*2.3*/'¿EN SU EMPRENDIMIENTO ELABORA ESTADOS FINANCIEROS',
    /*2.4*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR: ¿CUÁLES?',
    /*2.5*/'¿TIENE CLARIDAD SOBRE QUÉ PRESUPUESTOS DEBE ELABORAR PARA SU EMPRENDIMIENTO?',
    /*2.6*/'SI LA RESPUESTA ANTERIOR FUE AFIRMATIVA INDICAR: ¿CUÁLES?',
    /*2.10*/'¿LOS COSTOS DE SU PRODUCTO Y/O SERVICIO ESTÁN CLARAMENTE DEFINIDOS?',
    /*2.11*/'SI LA ANTERIOR RESPUESTA FUE AFIRMATIVA: ¿QUÉ FACTORES TIENE EN CUENTA PARA DEFINIR EL PRECIO DE SU PRODUCTO Y/O SERVICIO?',
    /*2.12*/'¿CUÁLES ALTERNATIVAS DE FINANCIAMIENTO USA PARA APOYAR SU EMPRENDIMIENTO?',
    /*2.13*/'¿SU PRODUCTO Y/O SERVICIO PRESENTA EN LA ACTUALIDAD VENTAS?',
    /*2.14*/'SI LA ANTERIOR RESPUESTA FUE AFIRMATIVA: ¿CUÁL ES EL VALOR PROMEDIO / ESTIMADO DE LAS VENTAS AL AÑO',
    /*2.15*/'¿CUÁLES CANALES DE VENTAS USA PARA COMERCIALIZAR SU NEGOCIO?',
    /*2.16*/'¿SABE CUÁLES OBLIGACIONES APLICAN A SU EMPRENDIMIENTO?',
    /*2.17*/'SI LA ANTERIOR RESPUESTA FUE AFIRMATIVA: ¿CUÁLES?',

    /*3. INFORMACIÓN DEL MERCADO*/

    /*3.1*/'¿TIENE CLARAMENTE DEFINIDO SUS CLIENTES ACTUALES',
    /*3.2*/'SI TIENE CLIENTES ACTUALES: ¿QUIÉNES SON?',
    /*3.3*/'¿TIENE DEFINIDO SUS CLIENTES POTENCIALES?',
    /*3.4*/'SI TIENE DEFINIDO SUS CLIENTES POTENCIALES: QUIÉNES SON?',
    /*3.5*/'¿TIENE DEFINIDO LOS COMPETIDORES DE SU PRODUCTO Y/O SERVICIO?',
    /*3.6*/'¿LE GUSTARÍA SER CÓMO?',
    /*3.7*/'¿NO TE GUSTARÍA SER CÓMO?',
    /*3.8*/'¿TIENE IDENTIFICADO CON CLARIDAD EL FACTOR DIFERENCIAL DE SU EMPRESA, PRODUCTO Y/O SERVICIO?',
    /*3.9*/'¿HA PARTICIPADO EN OTRAS ESTRATEGIAS DE FORTALECIMEINTO, SEMILLA, ACELERACIÓN Y/O SIMILARES?',
    /*3.10*/'¿ESTÁ DISPUESTO A REALIZAR ALIANZAS PARA LA VENTA Y/O DISTRIBUCIÓN DE SUS PRODUCTOS, ASÍ COMO PARA LA ADQUISICIÓN DE EQUIPOS, INSUMOS Y/O MATERIALES?',
    /*3.11*/'¿ESTÁ DISPUESTO A REALIZAR ALIANZAS Y/O CONVENIOS PARA EL USO DE EQUIPOS CON OTRAS INSTITUCIONES Y/U ORGANIZACIONES PARA EL DESARROLLO DE PRODUCTOS?',
    /*3.12*/'¿ESTÁ DISPUESTO A REALIZAR ALIANZA Y/O CONVENIOS PARA RECIBIR APOYO TÉCNICO ESPECIALIZADO PARA EL DESARROLLO DE PRODUCTOS Y/O SERVICIOS?',
    

    /* 4. INFORMACIÓN OPERATIVA TÉCNICA DE PRODUCTO Y/O SERVICIO*/
    /*4.1*/'¿La propuesta cuenta con una identificación básica de información científica susceptible de ser aplicada?',
        '¿Tiene al menos una imagen general de lo que debe hacer su producto y/o servicio?',
        '¿Tiene claridad en las necesidades para el desarrollo de su producto y/o servicio?',
        '¿Se cuenta con un aparente diseño que dé solución a la oportunidad detectada?',
        '¿Los elementos básicos del producto y/o servicio se encuentran identificados?',
        '¿Se cuenta con experiencia en el desarrollo de producto y/o servicios similares?',
        '¿Tiene algún cliente interesado ya en dicho producto y/o servicio?',
        '¿Se tiene claro los requerimientos legales para la puesta en marcha del producto y o servicio propuesto?',
        '¿El producto y/o servicio resuelve la necesidad del mercado de manera sostenible?',
        '¿Se cuenta con un modelo/prototipo de simulación del producto y/o servicio?',
        '¿Se tienen estrategias de mitigación de riesgos identificados?',
        '¿Los diseños del producto y/o servicio ya se encuentra validados en entorno controlado?',
        '¿se conoce lo que se necesita para implementar producto y o servicio?',
        '¿el producto y/o servicio fue validado por un laboratorio y está validación es favorable adecuada?',
        '¿Los costos de la propuestas ya se encuentran analizados?',
        '¿Tiene definidos los proveedores de insumos y materiales para la ejecución del producto y/o servicio?',
        '¿Tiene definido criterios para la selección de proveedores?',
        '¿El producto esta validado a nivel de detalle?',
        '¿han sido identificados los efectos adversos del producto y/o servicio?',
        '¿El producto y/o servicio ha sido validad en un entorno real?',
        '¿producto y/o servicio ya esta lista para la producción?',
        '¿El producto y/o servicio cuenta con documentación de usuario, mantenimiento y de servicio especificadas y controladas?',
        '¿El producto y/o servicio esta validado, comprobado y acreditado completamente?',
        '¿El producto y/o servicio cuenta con una producción estable?',
        '¿Se tiene la capacidad para desarrollar el producto y/o servicio?',
        '¿Implementa planes de producción?',
        '¿Implementa planes de compra?',
        '¿El producto y/o servicio se encuentra implementado y funcionando?',
        '¿Tienen parámetros de calidad definidos para su producto y/o servicio?',
        '¿El producto y/o servicio cuenta con patente, propiedad intelectual y/o industrial registrada?',
        '¿El producto y/o servicio cuenta con certificados de calidad, ambientales, otros?',
    /*4.2*/'¿CUENTA CON ÁREA O DEPARTAMENTO DE INNOVACIÓN Y/O DESARROLLO TECNOLÓGICO?',
    /*4.3*/'¿ TIENE DEFINIDAS LAS NECESIDADES DE SU PRODUCTO Y/O SERVICIO?',
    /*4.4*/'SI LA ANTERIOR RESPUESTA FUE AFIRMATIVA: ¿CUÁLES?',
    /*4.5*/'¿ TIENE DEFINIDAS LAS NECESIDADES DE SU EMPRENDIMIENTO (organizacional)?',
    /*4.6*/'SI LA ANTERIOR RESPUESTA FUE AFIRMATIVA: ¿CUÁLES?'
        ];
        foreach($preguntas as $pregunta){
            Preguntas::create(['name'=>$pregunta]);
        }
    }
}
