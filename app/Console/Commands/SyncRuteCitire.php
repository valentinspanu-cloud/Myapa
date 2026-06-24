<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RutaCitire;
use App\Services\CitiriOracleService;

class SyncRuteCitire extends Command
{
    protected $signature   = 'cititori:sync-rute {--max=30 : Numarul maxim de rute de testat}';
    protected $description = 'Descopera si importa rutele active din Oracle cu numarul total de contoare';

    public function __construct(private CitiriOracleService $oracle)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $luna = now()->month;
        $an   = now()->year;
        $max  = (int)$this->option('max');

        $this->info("Scanare rute Oracle (max {$max}) pentru {$luna}/{$an}...");

        $ruteGasite = [];

        for ($i = 1; $i <= $max; $i++) {
            $ruta = "TULCEA{$i}";
            $this->line("  → Testare {$ruta}...");

            $contoare = $this->oracle->getCitiri($ruta, $luna, $an);

            if (count($contoare) > 0) {
                $ruteGasite[] = $ruta;
                RutaCitire::updateOrCreate(
                    ['nume' => $ruta],
                    ['activa' => true]
                );
                $this->info("  ✓ {$ruta}: " . count($contoare) . " contoare");
            } else {
                $this->line("  — {$ruta}: goală");
            }
        }

        $this->info("\nRute active găsite: " . count($ruteGasite));
        $this->info(implode(', ', $ruteGasite));

        return self::SUCCESS;
    }
}
