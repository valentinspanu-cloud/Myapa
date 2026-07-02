<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\CitiriOracleService;

class SyncCititori extends Command
{
    protected $signature   = 'cititori:sync {--ruta= : Sincronizeaza doar o ruta specifica}';
    protected $description = 'Sincronizeaza contoarele si soldurile din Oracle in cache pentru cititori';

    public function __construct(private CitiriOracleService $oracle)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $luna = now()->day >= 21 ? now()->addMonthNoOverflow()->month : now()->month;
        $an   = now()->day >= 21 ? now()->addMonthNoOverflow()->year  : now()->year;

        // Aducem rutele active din userii cititori
        $rutaFilter = $this->option('ruta');

        $rute = User::role('cititor')
            ->whereNotNull('ruta')
            ->pluck('ruta')
            ->unique()
            ->values();

        if ($rutaFilter) {
            $rute = $rute->filter(fn($r) => $r === $rutaFilter)->values();
        }

        if ($rute->isEmpty()) {
            $this->warn('Nicio ruta gasita pentru userii cu rol cititor.');
            return self::SUCCESS;
        }

        $this->info("Sincronizare {$rute->count()} rute pentru {$luna}/{$an}...");

        foreach ($rute as $ruta) {
            $this->line("  → Ruta: {$ruta}");

            // Contoare
            $contoare = $this->oracle->getCitiri($ruta, $luna, $an);

            // Sortare dupa strada + nr_strada
            usort($contoare, function($a, $b) {
                $sA = strtolower($a['strada'] ?? '');
                $sB = strtolower($b['strada'] ?? '');
                if ($sA !== $sB) return strcmp($sA, $sB);
                return (int)($a['nr_strada'] ?? 0) - (int)($b['nr_strada'] ?? 0);
            });

            Cache::put("contoare_{$ruta}_{$luna}_{$an}", $contoare, now()->addHours(12));
            $this->line("    Contoare: " . count($contoare));

            // Solduri
            $solduri = [];
            foreach ($contoare as $contor) {
                $idClient = $contor['id_client'];
                if (!isset($solduri[$idClient])) {
                    $solduri[$idClient] = $this->oracle->getSold($idClient);
                }
            }

            Cache::put("solduri_{$ruta}_{$luna}_{$an}", $solduri, now()->addHours(4));
            $this->line("    Solduri: " . count($solduri));
        }

        $this->info('Sincronizare finalizata!');
        return self::SUCCESS;
    }
}
