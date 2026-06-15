<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ExternClientiExport implements FromQuery, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    public function query()
    {
        return DB::table('invoice_extern_clients')->orderBy('nume');
    }

    public function headings(): array
    {
        return [
            'Cod Client',
            'Nume',
            'Email',
            'Nr. Contract',
            'Client ID (Oracle)',
            'Adăugat La',
        ];
    }

    public function map($row): array
    {
        return [
            $row->cod_client,
            $row->nume,
            $row->email,
            $row->contract_nr,
            $row->client_id,
            $row->created_at,
        ];
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
