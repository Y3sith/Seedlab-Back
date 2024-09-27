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
        $datos = [];

        foreach ($this->data as $dato) {
            $fila = [
                $dato['pregunta'] ?? 'Pregunta desconocida',
                $dato['opcion'] ?? 'Sin respuesta',
                $dato['subpregunta'] ?? 'No contiene subpregunta',
                $dato['respuesta_texto'] ?? 'Sin respuesta',
                $dato['valor'] ?? 'N/A',
                $dato['fecha_reg'] ?? 'N/A',
            ];

            // Si es la primera hoja, agregar los puntajes
            if ($this->isFirstSheet) {
                $fila[] = $dato['info_general'] ?? 'N/A';
                $fila[] = $dato['info_financiera'] ?? 'N/A';
                $fila[] = $dato['info_mercado'] ?? 'N/A';
                $fila[] = $dato['info_trl'] ?? 'N/A';
                $fila[] = $dato['info_tecnica'] ?? 'N/A';
            }

            $datos[] = $fila;
        }

        return $datos;
    }


    public function headings(): array
    {
        $headings = [
            'Pregunta',
            'Respuesta Pregunta',
            'Subpregunta',
            'Respuesta Subpregunta',
            'Valor Respuesta',
            'Fecha realización formulario',
        ];

        // Si es la primera hoja, agregar los encabezados de los puntajes
        if ($this->isFirstSheet) {
            $headings = array_merge($headings, [
                'Info General',
                'Info Financiera',
                'Info Mercado',
                'Info TRL',
                'Info Técnica',
            ]);
        }

        return $headings;
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:L1');

        $sheet->setCellValue('A1', $this->title);
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
        return $this->title;
    }
}
