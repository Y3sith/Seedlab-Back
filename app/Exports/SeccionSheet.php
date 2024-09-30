<?php

namespace App\Exports;

use App\Models\Respuesta;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeccionSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $data;
    protected $title;

    protected $isFirstSheet;

    public function __construct(array $data, $title, $isFirstSheet)
    {
        $this->data = $data;
        $this->title = $title;
        $this->isFirstSheet = $isFirstSheet;
    }

    public function array(): array
    {
        // Inicializa un array para almacenar los datos
        $datos = [];

        // Itera sobre cada dato en la colección de datos
        foreach ($this->data as $dato) {
            // Crea una fila con los valores del dato, usando valores predeterminados si faltan
            $fila = [
                $dato['pregunta'] ?? 'Pregunta desconocida', // Pregunta
                $dato['opcion'] ?? 'Sin respuesta', // Opción de respuesta
                $dato['subpregunta'] ?? 'No contiene subpregunta', // Subpregunta
                $dato['respuesta_texto'] ?? 'Sin respuesta', // Respuesta en texto
                $dato['valor'] ?? 'N/A', // Valor asociado
                $dato['fecha_reg'] ?? 'N/A', // Fecha de registro
            ];

            // Si es la primera hoja, agregar los puntajes adicionales
            if ($this->isFirstSheet) {
                $fila[] = $dato['info_general'] ?? 'N/A'; // Información general
                $fila[] = $dato['info_financiera'] ?? 'N/A'; // Información financiera
                $fila[] = $dato['info_mercado'] ?? 'N/A'; // Información de mercado
                $fila[] = $dato['info_trl'] ?? 'N/A'; // Información de TRL
                $fila[] = $dato['info_tecnica'] ?? 'N/A'; // Información técnica
            }

            // Agrega la fila construida al array de datos
            $datos[] = $fila;
        }

        // Retorna el array de datos
        return $datos;
    }


    public function headings(): array
    {
        // Inicializa un array con los encabezados básicos de la tabla
        $headings = [
            'Pregunta', // Encabezado para las preguntas
            'Respuesta Pregunta', // Encabezado para las respuestas a las preguntas
            'Subpregunta', // Encabezado para las subpreguntas
            'Respuesta Subpregunta', // Encabezado para las respuestas a las subpreguntas
            'Valor Respuesta', // Encabezado para los valores de respuesta
            'Fecha realización formulario', // Encabezado para la fecha de realización del formulario
        ];

        // Si es la primera hoja, agregar los encabezados de los puntajes adicionales
        if ($this->isFirstSheet) {
            $headings = array_merge($headings, [
                'Info General', // Encabezado para información general
                'Info Financiera', // Encabezado para información financiera
                'Info Mercado', // Encabezado para información de mercado
                'Info TRL', // Encabezado para información de TRL
                'Info Técnica', // Encabezado para información técnica
            ]);
        }

        // Retorna el array de encabezados
        return $headings;
    }


    public function styles(Worksheet $sheet)
    {
        // Combina las celdas A1 a L1 para el título
        $sheet->mergeCells('A1:L1');

        // Establece el valor de la celda A1 como el título de la hoja
        $sheet->setCellValue('A1', $this->title);

        // Aplica negrita al texto del título y centra la alineación
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Establecer el encabezado en la fila 2
        $sheet->fromArray($this->headings(), NULL, 'A2');
        $sheet->getStyle('A2:L2')->getFont()->setBold(true);
        $sheet->getStyle('A2:L2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Ajustar el ancho de las columnas
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        // Retorna el título de la hoja que se está exportando
        return $this->title;
    }
}
