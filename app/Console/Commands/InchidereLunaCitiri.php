<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CitireContor;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InchidereLunaCitiri extends Command
{
    protected $signature   = 'cititori:inchidere-luna {--luna=} {--an=}';
    protected $description = 'Marcheaza citirile neconfirmate din luna anterioara si trimite email';

    public function handle(): int
    {
        $luna = $this->option('luna') ?? now()->subMonth()->month;
        $an   = $this->option('an')   ?? now()->subMonth()->year;

        $this->info("Inchidere luna {$luna}/{$an}...");

        // Citiri neconfirmate
        $neconfirmate = CitireContor::where('luna', $luna)
            ->where('an', $an)
            ->whereIn('status', ['nou', 'eroare', 'respins'])
            ->get();

        $this->info("Citiri neconfirmate: " . $neconfirmate->count());

        if ($neconfirmate->count() > 0) {
            // Marcam ca expirat
            CitireContor::where('luna', $luna)
                ->where('an', $an)
                ->whereIn('status', ['nou', 'eroare', 'respins'])
                ->update(['status' => 'expirat']);

            // Trimitem email
            $continut = "Raport citiri neconfirmate pentru {$luna}/{$an}\n\n";
            $continut .= "Total neconfirmate: " . $neconfirmate->count() . "\n\n";
            $continut .= str_pad("Cod", 12) . str_pad("Rută", 10) . str_pad("Index", 10) . "Cititor\n";
            $continut .= str_repeat("-", 50) . "\n";
            foreach ($neconfirmate as $c) {
                $continut .= str_pad($c->cod_abonat, 12) . str_pad($c->ruta, 10) . str_pad($c->index_citit ?? '-', 10) . ($c->cititor?->name ?? '-') . "\n";
            }

            Mail::raw($continut, function($msg) use ($luna, $an) {
                $msg->to('contractare@aquaservtulcea.ro')
                    ->subject("Citiri neconfirmate {$luna}/{$an} — Aquaserv Tulcea");
            });

            $this->info("Email trimis la contractare@aquaservtulcea.ro");
        }

        // Statistici finale
        $confirmate = CitireContor::where('luna', $luna)->where('an', $an)->where('status', 'confirmat')->count();
        $this->info("Confirmate: {$confirmate}");

        return self::SUCCESS;
    }
}
