<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacturiExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $facturi;
    protected string $luna;

    public function __construct(array $facturi, string $luna)
    {
        $this->facturi = $facturi;
        $this->luna    = $luna;
    }

    public function headings(): array
    {
        return [
            '#',
            'Cod Client',
            'Nume',
            'Email',
            'Nr. Factură',
            'Data Emitere',
            'Scadență',
            'Sursă',
            'PDF',
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->facturi as $i => $f) {
            $rows[] = [
                $i + 1,
                $f['cod_client'],
                $f['nume'] ?? '—',
                $f['email'] ?? 'fără email',
                $f['nr_factura'],
                $f['data_emitere'],
                $f['scadenta'],
                $f['sursa'],
                $f['pdf_name'],
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a5276']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
