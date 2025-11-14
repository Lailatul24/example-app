<?php

namespace App\Exports;

use App\Models\Facility;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacilitiesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Facility::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Name',
            'Condition',
            'Quantity Total',
            'Quantity Available',
            'Description',
            'Created At',
        ];
    }
}
