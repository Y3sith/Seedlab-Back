<?php

namespace App\Exports;

use App\Models\Asesor;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AsesoresAliadosExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id_aliado;
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    // Constructor que inicializa las propiedades con los parámetros recibidos
        public function __construct($id_aliado, $tipo_reporte, $fecha_inicio, $fecha_fin){
        $this->id_aliado = $id_aliado;
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    // Método que obtiene la colección de datos para la exportación
    public function collection() {
        // Construcción de la consulta para obtener datos de asesores
        $query=DB::table($this->tipo_reporte)
            ->join('aliado', 'asesor.id_aliado', '=', 'aliado.id') // Join con la tabla 'aliado'
            ->join('users', 'asesor.id_autentication', '=', 'users.id') // Join con la tabla 'users'
            ->select(
                'asesor.nombre',           // Nombre del asesor
                'asesor.apellido',         // Apellido del asesor
                'asesor.documento',        // Documento del asesor
                'asesor.celular',          // Celular del asesor
                'asesor.fecha_nac',        // Fecha de nacimiento del asesor
                'asesor.direccion',        // Dirección del asesor
                'users.email',             // Correo del usuario asociado
                'users.fecha_registro',    // Fecha de registro del usuario
                DB::raw('(CASE WHEN users.estado = 1 THEN "Activo" ELSE "Inactivo" END) as estado')
            )
            ->where('asesor.id_aliado', $this->id_aliado);

        // Filtrar por rango de fechas solo si están definidas
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);
        }
        return $query->get();
    }

    // Método que define los encabezados de la exportación
    public function headings(): array {
        return [
            'Nombre',                   // Nombre del asesor
            'Apellido',                 // Apellido del asesor
            'Documento',                // Documento del asesor
            'Celular',                  // Celular del asesor
            'Fecha de Nacimiento',      // Fecha de nacimiento del asesor
            'Dirección',                // Dirección del asesor
            'Correo',                   // Correo del usuario asociado
            'Fecha de Registro',        // Fecha de registro del usuario
            'Estado',                   // Estado del usuario
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
