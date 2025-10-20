<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $workers;

    public function __construct($workers)
    {
        $this->workers = $workers;
    }

    /**
     * Return the collection of workers to export
     */
    public function collection()
    {
        return collect($this->workers);
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Passport Number',
            'Position',
            'Country',
            'Current Client',
            'Passport Expiry',
            'Permit Expiry',
            'Status',
        ];
    }

    /**
     * Map each worker to spreadsheet row
     */
    public function map($worker): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $worker['name'],
            $worker['passport'],
            $worker['position'],
            $worker['country'],
            $worker['client'],
            $worker['passport_expiry'],
            $worker['permit_expiry'],
            $worker['status'],
        ];
    }

    /**
     * Apply styles to the spreadsheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (heading)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo color
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,  // No
            'B' => 30, // Name
            'C' => 20, // Passport Number
            'D' => 20, // Position
            'E' => 20, // Country
            'F' => 30, // Current Client
            'G' => 18, // Passport Expiry
            'H' => 18, // Permit Expiry
            'I' => 12, // Status
        ];
    }
}
