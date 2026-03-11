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
        return [
            ['211040404', 'E-1A-1-2C', 'SERVICE KIT 707-99-27500', 1, 'PC', 0, '', ''],
            ['202040277', 'E-1A-1-5A', 'MOUNTING RADIATOR 2635A052 PERKINS', 10, 'PC', 2, 50, ''],
        ];
    }
}
