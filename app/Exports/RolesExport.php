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
    protected $tipo_reporte; // Tipo de reporte que se va a generar
    protected $fecha_inicio; // Fecha de inicio para filtrar resultados
    protected $fecha_fin;    // Fecha de fin para filtrar resultados

    // Constructor que recibe tipo de reporte y fechas
    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->tipo_reporte = $tipo_reporte; // Asigna el tipo de reporte
        $this->fecha_inicio = $fecha_inicio; // Asigna la fecha de inicio
        $this->fecha_fin = $fecha_fin;       // Asigna la fecha de fin
    }

    // Método que obtiene la colección de datos
    public function collection()
    {
        // Validación de tipo_reporte
        $validTipos = ['emprendedor', 'orientador', 'empresa']; // Tipos de reporte válidos
        if (!in_array($this->tipo_reporte, $validTipos)) {
            throw new \Exception("Tipo de reporte no válido."); // Lanza excepción si el tipo no es válido
        }

        // Validación de fechas
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            throw new \Exception("Fechas de inicio y fin son requeridas."); // Lanza excepción si las fechas son requeridas
        }

        // Construcción de la consulta
        $query = DB::table('users')
            ->join($this->tipo_reporte, 'users.id', '=', $this->tipo_reporte . '.id_autentication') // Realiza un join con la tabla correspondiente
            ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', "{$this->tipo_reporte}.*") // Selecciona los campos deseados
            ->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]); // Filtra por fecha

        return $query->get(); // Retorna los resultados de la consulta
    }

    // Método que define los encabezados de la exportación
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

    // Método que mapea los datos de cada rol a un array
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
                $columns = ['A', 'B', 'C', 'D', 'E', 'F','G','H','I','J','K']; 
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Aplicar estilos adicionales si es necesario
                $sheet->getStyle('A1:K1')->getFont()->setBold(true);
                $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
