<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateProdukExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['KOMAT', 'MAPPING', 'Material Description', 'Stock SAP', 'UoM', 'Min Stock', 'Max Stock', 'Supplier ID'];
    }

    public function array(): array
    {
        return [];
    }
}
