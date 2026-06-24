<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Abonat;
use App\Models\StradaGeocodata;
use Illuminate\Support\Facades\Http;

class GeocodeStrazi extends Command
{
    protected $signature   = 'cititori:geocode-strazi {--ruta= : Geocodeaza doar o ruta specifica} {--forceaza : Reincearca si strazile esuate anterior}';
    protected $description = 'Geocodeaza strazile distincte din rute folosind Nominatim (OpenStreetMap), respectand localitatea reala';

    public function handle(): int
    {
        $rutaFilter = $this->option('ruta');
        $forceaza   = $this->option('forceaza');

        $query = Abonat::select('ruta', 'strada', 'localitate')
            ->whereNotNull('strada')
            ->where('strada', '!=', '')
            ->distinct();

        if ($rutaFilter) {
            $query->where('ruta', $rutaFilter);
        }

        $strazi = $query->get();
        $this->info("Strazi distincte de geocodat: {$strazi->count()}");

        $ok = 0;
        $fail = 0;
        $skip = 0;

        foreach ($strazi as $s) {
            $localitate = $s->localitate ?: 'Tulcea';

            $exists = StradaGeocodata::where('ruta', $s->ruta)
                ->where('nume_strada', $s->strada)
                ->where('localitate', $localitate)
                ->exists();

            if ($exists && !$forceaza) {
                $skip++;
                continue;
            }

            $this->line("  → {$s->ruta} / {$s->strada} ({$localitate})");

            $data = $this->cautaStrada($s->strada, $localitate);

            // Retry cu diacritice adaugate daca prima cautare a esuat
            if (empty($data)) {
                $cuDiacritice = $this->adaugaDiacritice($s->strada);
                if ($cuDiacritice !== $s->strada) {
                    sleep(1);
                    $data = $this->cautaStrada($cuDiacritice, $localitate);
                }
            }

            if (!empty($data) && isset($data[0]['geojson'])) {
                StradaGeocodata::updateOrCreate(
                    ['ruta' => $s->ruta, 'nume_strada' => $s->strada, 'localitate' => $localitate],
                    [
                        'geojson'     => json_encode($data[0]['geojson']),
                        'geocodat_la' => now(),
                    ]
                );
                $ok++;
            } else {
                $this->warn("    Nu s-a gasit geometrie pentru: {$s->strada} ({$localitate})");
                $fail++;
            }

            sleep(1);
        }

        $this->info("\nGeocodare finalizata: {$ok} ok, {$fail} esec, {$skip} deja existente");
        return self::SUCCESS;
    }

    private function cautaStrada(string $strada, string $localitate): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'AquaservTulceaCititori/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'street'          => $strada,
                'city'            => $localitate,
                'state'           => 'Tulcea',
                'country'         => 'Romania',
                'format'          => 'json',
                'polygon_geojson' => 1,
                'limit'           => 1,
            ]);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function adaugaDiacritice(string $strada): string
    {
        $map = [
            'Closca'    => 'Cloșca',
            'Crisan'    => 'Crișan',
            'Horia'     => 'Horea',
            'Sevcenco'  => 'Șevcenko',
            'Maramures' => 'Maramureș',
            'Marasesti' => 'Mărășești',
        ];

        return $map[$strada] ?? $strada;
    }
}
