<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class TotiClientiExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Cod Client',
            'Nume',
            'Email',
            'Telefon',
            'Nr. Contract',
            'Client ID (Oracle)',
            'Sursă',
        ];
    }

    public function array(): array
    {
        $rows = [];

        // Clienți din portal
        $portal = DB::table('client_codes')
            ->join('users', 'users.id', '=', 'client_codes.user_id')
            ->select(
                'client_codes.client_code',
                'users.name',
                'users.email',
                'users.phone',
                'client_codes.contract_nr',
                'client_codes.client_id'
            )
            ->orderBy('users.name')
            ->get();

        foreach ($portal as $c) {
            $rows[] = [
                $c->client_code,
                $c->name,
                $c->email ?? '—',
                $c->phone ?? '—',
                $c->contract_nr,
                $c->client_id,
                'Portal',
            ];
        }

        // Clienți externi
        $externi = DB::table('invoice_extern_clients')->orderBy('nume')->get();

        foreach ($externi as $c) {
            $rows[] = [
                $c->cod_client,
                $c->nume,
                $c->email,
                '—',
                $c->contract_nr,
                $c->client_id,
                'Extern',
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
