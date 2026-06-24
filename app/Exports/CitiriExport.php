<?php

namespace App\Exports;

use App\Models\CitireContor;
use App\Models\Abonat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CitiriExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private int $luna,
        private int $an,
        private ?string $ruta = null,
        private ?string $status = null
    ) {}

    public function collection()
    {
        $query = CitireContor::with('cititor')
            ->where('luna', $this->luna)
            ->where('an', $this->an)
            ->orderBy('ruta')
            ->orderBy('cod_abonat');

        if ($this->ruta)   $query->where('ruta', $this->ruta);
        if ($this->status) $query->where('status', $this->status);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Cod Abonat', 'Nume', 'Strada', 'Nr', 'Ruta',
            'Cititor', 'Index Vechi', 'Index Citit', 'Consum',
            'Status', 'Data Citire', 'GPS Lat', 'GPS Lng', 'Observatii'
        ];
    }

    public function map($citire): array
    {
        $abonat = Abonat::where('cod_abonat', $citire->cod_abonat)->first();
        return [
            $citire->cod_abonat,
            $abonat?->nume_abonat ?? '',
            $abonat?->strada ?? '',
            $abonat?->nr_strada ?? '',
            $citire->ruta,
            $citire->cititor?->name ?? '',
            $citire->index_vechi,
            $citire->index_citit,
            $citire->consum,
            $citire->status,
            $citire->created_at?->format('d.m.Y H:i'),
            $citire->gps_lat,
            $citire->gps_lng,
            $citire->observatii,
        ];
    }
}
