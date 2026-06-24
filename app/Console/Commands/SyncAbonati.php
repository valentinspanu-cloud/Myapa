<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Abonat;
use App\Models\RutaCitire;
use App\Services\CitiriOracleService;

class SyncAbonati extends Command
{
    protected $signature   = 'cititori:sync-abonati {--ruta= : Sincronizeaza doar o ruta specifica}';
    protected $description = 'Sincronizeaza tabela abonati din Oracle (nume, adresa, contor)';

    public function __construct(private CitiriOracleService $oracle)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $luna       = now()->month;
        $an         = now()->year;
        $rutaFilter = $this->option('ruta');

        $rute = RutaCitire::active()->orderBy('nume')->get();

        if ($rutaFilter) {
            $rute = $rute->filter(fn($r) => $r->nume === $rutaFilter)->values();
        }

        if ($rute->isEmpty()) {
            $this->warn('Nicio ruta activa gasita.');
            return self::SUCCESS;
        }

        $this->info("Sincronizare abonati pentru {$rute->count()} rute...");
        $total = 0;

        foreach ($rute as $ruta) {
            $this->line("  → Ruta: {$ruta->nume}");

            $contoare = $this->oracle->getCitiri($ruta->nume, $luna, $an);
            $this->line("    Contoare: " . count($contoare));

            foreach ($contoare as $c) {
                Abonat::updateOrCreate(
                    ['cod_abonat' => $c['cod_abonat'], 'id_locatie' => $c['id_locatie']],
                    [
                        'id_client'    => $c['id_client'],
                        'id_locatie'   => $c['id_locatie'],
                        'nume_abonat'  => $c['nume_abonat'],
                        'adresa'       => $c['adresa'],
                        'localitate'   => $c['localitate'] ?? 'TULCEA',
                        'strada'       => $c['strada'],
                        'nr_strada'    => $c['nr_strada'],
                        'bloc'         => $c['bloc'],
                        'addr_stair'   => $c['addr_stair'],
                        'addr_apt'     => $c['addr_apt'],
                        'ruta'         => $c['ruta'],
                        'sector'       => $c['sector'],
                        'sincronizat_la' => now(),
                    ]
                );
                $total++;
            }

            $this->info("    ✓ {$ruta->nume}: " . count($contoare) . " abonati sincronizati");
        }

        $this->info("\nTotal abonati sincronizati: {$total}");
        return self::SUCCESS;
    }
}
