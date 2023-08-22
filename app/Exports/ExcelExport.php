<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExcelExport implements FromCollection,WithHeadings,WithMapping
{
    protected $resultados;


    public function __construct($resultados)
    {
        $this->resultados = $resultados;
    }

    public function collection()
    {
        return $this->resultados;
    }

    public function headings(): array
    {
        return [
        'Código'

     ];
    }

    public function map($cve): array
    {
        return [
            $cve->codigo

        ];
    }
}
