<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class RolesExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {

        // Validación de tipo_reporte
        $validTipos = ['emprendedor', 'orientador', 'empresa'];
        if (!in_array($this->tipo_reporte, $validTipos)) {
            throw new \Exception("Tipo de reporte no válido.");
        }

        // Validación de fechas
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            throw new \Exception("Fechas de inicio y fin son requeridas.");
        }

        $query = DB::table('users')
            ->join($this->tipo_reporte, 'users.id', '=', $this->tipo_reporte . '.id_autentication')
            ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', "{$this->tipo_reporte}.*")
            ->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Email',
            'Fecha de Registro',
            'Estado',
            'Nombre',
            'Apellido',
            'Documento',
            'N° Celular',
            'Genero',
            'Fecha de Nacimiento',
            'Dirección',
        ];
    }

    public function map($rol): array
    {
        return [
            $rol->id,
            $rol->email,
            $rol->fecha_registro,
            $rol->estado,
            $rol->nombre,
            $rol->apellido,
            $rol->documento,
            $rol->celular,
            $rol->genero,
            $rol->fecha_nac,
            $rol->direccion,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $columns = ['A', 'B', 'C', 'D', 'E']; // Asume que tienes cinco columnas
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Aplicar estilos adicionales si es necesario
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
