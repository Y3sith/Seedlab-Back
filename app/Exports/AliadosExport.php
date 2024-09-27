<?php

namespace App\Exports;

use App\Models\Aliado;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AliadosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    // Constructor que inicializa las propiedades con los parámetros recibidos
    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin) {
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    // Método que obtiene la colección de datos para la exportación
    public function collection() {
        // Validación de las fechas de inicio y fin
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            throw new \Exception("Fechas de inicio y fin son requeridas.");
        }

        // Construcción de la consulta para obtener datos de aliados
        $query = DB::table('users')
            ->join($this->tipo_reporte, 'users.id', '=', $this->tipo_reporte.'.id_autentication') // Join con la tabla del tipo de reporte
            ->select('users.id', 'users.email', 'users.fecha_registro', 
                DB::raw('(CASE WHEN users.estado = 1 THEN "Activo" ELSE "Inactivo" END) as estado'), // Estado como Activo/Inactivo
                "{$this->tipo_reporte}.nombre" // Nombre del aliado
            );

        // Filtrar por rango de fechas solo si están definidos ambos
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->get();
    }

    // Método que define los encabezados de la exportación
    public function headings(): array {
        return [
            'Nombre',                   // Encabezado para el nombre
            'Correo',                   // Encabezado para el correo
            'Fecha de Registro',        // Encabezado para la fecha de registro
            'Estado',                   // Encabezado para el estado
        ];
    }

    // Método que mapea los datos de cada aliado
    public function map($aliado): array {
        return [
            $aliado->nombre,           // Nombre del aliado
            $aliado->email,            // Correo del aliado
            $aliado->fecha_registro,   // Fecha de registro del aliado
            $aliado->estado,           // Estado del aliado
        ];
    }
}
