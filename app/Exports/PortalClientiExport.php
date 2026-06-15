<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class PortalClientiExport implements FromQuery, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    public function query()
    {
        return DB::table('client_codes')
            ->join('users', 'users.id', '=', 'client_codes.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.status',
                'users.category',
                'users.notify',
                'users.notify_sms',
                'users.notify_invoice',
                'users.email_verified_at',
                'users.created_at',
                'client_codes.client_code',
                'client_codes.contract_nr',
                'client_codes.client_id'
            )
            ->orderBy('users.name');
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Nume',
            'Email',
            'Telefon',
            'Status',
            'Categorie',
            'Notif. Email',
            'Notif. SMS',
            'Notif. Factură',
            'Email Verificat',
            'Înregistrat La',
            'Cod Client',
            'Nr. Contract',
            'Client ID (Oracle)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->phone ?? '—',
            $row->status ?? '—',
            $row->category ?? '—',
            $row->notify     ? 'Da' : 'Nu',
            $row->notify_sms ? 'Da' : 'Nu',
            $row->notify_invoice ? 'Da' : 'Nu',
            $row->email_verified_at ?? 'Neverificat',
            $row->created_at,
            $row->client_code,
            $row->contract_nr,
            $row->client_id,
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
