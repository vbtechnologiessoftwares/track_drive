<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaticDataExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Define your static data here
        return collect([
            ['ID', 'Name', 'Email'],
            [1, 'John Doe', 'john.doe@example.com'],
            [2, 'Jane Smith', 'jane.smith@example.com'],
            [3, 'Michael Johnson', 'michael.johnson@example.com'],
        ]);
    }

    public function headings(): array
    {
        // Define headings for the columns
        return [
            'ID',
            'Name',
            'Email',
        ];
    }
}
?>