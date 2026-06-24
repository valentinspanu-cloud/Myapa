<?php

namespace App\Jobs;

use App\Mail\FacturaNotificare;
use App\Http\Controllers\ApiController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TrimiteFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 0;

    protected array $factura;
    protected string $batchId;

    public function __construct(array $factura, string $batchId)
    {
        $this->factura = $factura;
        $this->batchId = $batchId;
    }

    public function handle(): void
    {
        $f = $this->factura;

        if (empty($f['email'])) {
            Log::warning("TrimiteFactura: email lipsă pentru {$f['cod_client']}");
            return;
        }

        if (!file_exists($f['pdf_path'])) {
            Log::error("TrimiteFactura: PDF lipsă pentru {$f['nr_factura']}: {$f['pdf_path']}");
            $this->logResult($f, 'eroare', 'PDF lipsă');
            return;
        }

        try {
            // Obține sold din Oracle
            $sold = $this->getSold($f['cod_client'], $f['client_id']);

            Mail::to($f['email'])->send(new FacturaNotificare([
                'nume'         => $f['nume'],
                'cod_client'   => $f['cod_client'],
                'nr_factura'   => $f['nr_factura'],
                'data_emitere' => $f['data_emitere'],
                'scadenta'     => $f['scadenta'],
                'sold'         => number_format($sold, 2, '.', ''),
                'luna'         => $f['luna'],
            ], $f['pdf_path']));

            $this->logResult($f, 'trimis');
            Log::info("Factură trimisă [{$this->batchId}]: {$f['nr_factura']} → {$f['email']}");

            // Sleep 10 secunde între mail-uri
            sleep(10);

        } catch (\Exception $e) {
            $this->logResult($f, 'eroare', $e->getMessage());
            Log::error("Eroare factură {$f['nr_factura']}: " . $e->getMessage());
            throw $e; // permite retry
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->logResult($this->factura, 'eroare', 'Job eșuat după toate retry-urile: ' . $e->getMessage());
    }

    private function getSold(string $codClient, ?string $clientId): float
    {
        if (!$clientId) return 0;
        try {
            $sold = ApiController::cURL('sold', [
                'idfirma'   => $clientId,
                'datasold'  => date('d-M-Y'),
                'i_iddocei' => null,
                'i_codloc'  => null,
            ]);
            if (empty($sold['items'])) return 0;
            return (float) $sold['items'][0]['sold'];
        } catch (\Exception $e) {
            Log::warning("Sold indisponibil pentru {$codClient}: " . $e->getMessage());
            return 0;
        }
    }

    private function logResult(array $f, string $status, ?string $eroare = null): void
    {
        DB::table('invoice_send_log')->insert([
            'cod_client' => $f['cod_client'],
            'nr_factura' => $f['nr_factura'],
            'email'      => $f['email'] ?? '',
            'status'     => $status,
            'eroare'     => $eroare,
            'trimis_la'  => now(),
        ]);

        // Actualizează progresul batch-ului
        DB::table('invoice_send_batches')
            ->where('batch_id', $this->batchId)
            ->increment($status === 'trimis' ? 'trimise' : 'esuate');
    }
}
