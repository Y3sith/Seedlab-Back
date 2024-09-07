<?php

namespace App\Exports;

use App\Models\Respuesta;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class SeccionSheet implements FromArray, WithTitle
{
    protected $data;
    protected $title;

    public function __construct(array $data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title;
    }
}
