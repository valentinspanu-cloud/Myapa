<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Mail\FacturaNotificare;
use App\Models\InvoiceExternClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\TrimiteFactura;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacturiExport;

class FacturaBulkController extends Controller
{
    /**
     * Afișează pagina principală cu preview facturi pentru luna selectată
     */
    public function index(Request $request)
    {
        $luna_sel = (int) $request->get('luna', date('n'));
        $an_sel   = (int) $request->get('an',   date('Y'));

        $facturi  = $this->getFaturiPentruLuna($luna_sel, $an_sel);
        $lunaText = Carbon::createFromDate($an_sel, $luna_sel, 1)->locale('ro')->translatedFormat('F Y');
        $trimise  = session('trimise', null);
        $esuate   = session('esuate', []);

        // Generează lista de luni disponibile (PDF-uri existente în folder)
        $luniDisponibile = $this->getLuniDisponibile();

        // Verifică dacă există un batch activ
        $activeBatch = DB::table('invoice_send_batches')
            ->where('status', 'in_progress')
            ->orderByDesc('started_at')
            ->first();

        return view('admin.facturi.index', compact(
            'facturi', 'lunaText', 'trimise', 'esuate',
            'luna_sel', 'an_sel', 'luniDisponibile', 'activeBatch'
        ));
    }

    /**
     * Trimite mail-urile async via queue
     */
    public function trimite(Request $request)
    {
        $luna_sel = (int) $request->get('luna', date('n'));
        $an_sel   = (int) $request->get('an',   date('Y'));

        $selected     = $request->get('selected', []);
        $toateFacturi = $this->getFaturiPentruLuna($luna_sel, $an_sel);

        // Filtrează doar cele selectate și cu email
        if (!empty($selected)) {
            $facturi = array_filter($toateFacturi, fn($f) => in_array($f['pdf_name'], $selected) && !empty($f['email']));
        } else {
            $facturi = array_filter($toateFacturi, fn($f) => !empty($f['email']));
        }

        if (empty($facturi)) {
            return redirect()->route('admin.facturi.index', ['luna' => $luna_sel, 'an' => $an_sel])
                ->with('error', 'Nu există facturi de trimis.');
        }

        // Creează batch pentru tracking progres
        $batchId  = 'facturi_' . date('YmdHis');
        $lunaText = \Carbon\Carbon::createFromDate($an_sel, $luna_sel, 1)->locale('ro')->translatedFormat('F Y');

        DB::table('invoice_send_batches')->insert([
            'batch_id'   => $batchId,
            'luna'       => $lunaText,
            'total'      => count($facturi),
            'started_at' => now(),
        ]);

        // Dispatch job pentru fiecare factură
        foreach ($facturi as $f) {
            TrimiteFactura::dispatch($f, $batchId);
        }

        Log::info("Batch {$batchId}: " . count($facturi) . " job-uri dispatched pentru {$lunaText}");

        return redirect()->route('admin.facturi.index', ['luna' => $luna_sel, 'an' => $an_sel])
            ->with('batch_id', $batchId)
            ->with('batch_total', count($facturi))
            ->with('batch_luna', $lunaText);
    }

    /**
     * Status progres batch (AJAX)
     */
    public function batchStatus(Request $request)
    {
        $batchId = $request->get('batch_id');
        $batch   = DB::table('invoice_send_batches')->where('batch_id', $batchId)->first();

        if (!$batch) {
            return response()->json(['error' => 'Batch negăsit']);
        }

        $procesat = $batch->trimise + $batch->esuate;

        // Marchează ca finalizat dacă s-a procesat tot
        if ($procesat >= $batch->total && $batch->status === 'in_progress') {
            DB::table('invoice_send_batches')->where('batch_id', $batchId)->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
            $batch->status = 'completed';
        }

        return response()->json([
            'total'     => $batch->total,
            'trimise'   => $batch->trimise,
            'esuate'    => $batch->esuate,
            'procesat'  => $procesat,
            'status'    => $batch->status,
            'procent'   => $batch->total > 0 ? round(($procesat / $batch->total) * 100) : 0,
        ]);
    }


    /**
     * Log mailuri trimise
     */
    public function log(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $logs = DB::table('invoice_send_log')
            ->when($search, fn($q) => $q->where('cod_client', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('nr_factura', 'like', "%{$search}%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('trimis_la')
            ->paginate(100);

        $totalTrimise = DB::table('invoice_send_log')->where('status', 'trimis')->count();
        $totalEsuate  = DB::table('invoice_send_log')->where('status', 'eroare')->count();

        return view('admin.facturi.log', compact('logs', 'totalTrimise', 'totalEsuate'));
    }

    /**
     * Export preview facturi în Excel
     */
    public function export(Request $request)
    {
        $luna_sel = (int) $request->get('luna', date('n'));
        $an_sel   = (int) $request->get('an',   date('Y'));

        $facturi  = $this->getFaturiPentruLuna($luna_sel, $an_sel);
        $lunaText = Carbon::createFromDate($an_sel, $luna_sel, 1)->locale('ro')->translatedFormat('F Y');
        $fileName = 'facturi_' . str_pad($luna_sel, 2, '0', STR_PAD_LEFT) . '_' . $an_sel . '.xlsx';

        return Excel::download(new FacturiExport($facturi, $lunaText), $fileName);
    }

    /**
     * Scanează folderul invoices și returnează facturile pentru luna/an dat
     */
    private function getFaturiPentruLuna(int $luna, int $an): array
    {
        $facturi = [];

        // Caută în root și în subdirectorul specific lunii/anului
        $subdir = storage_path('app/invoices/' . $an . '/' . str_pad($luna, 2, '0', STR_PAD_LEFT));
        $files = array_merge(
            glob(storage_path('app/invoices/*.pdf')) ?: [],
            glob($subdir . '/*.pdf') ?: []
        );
        if (empty($files)) return [];

        // Încarcă toți clienții din portal
        $portalClients = DB::table('client_codes')
            ->join('users', 'users.id', '=', 'client_codes.user_id')
            ->select('client_codes.client_code', 'client_codes.client_id', 'users.name', 'users.email')
            ->get()
            ->keyBy('client_code');

        // Încarcă clienții externi
        $externClients = InvoiceExternClient::all()->keyBy('cod_client');

        foreach ($files as $filePath) {
            $fileName = basename($filePath);

            // Format: {cod_client}_{nr_factura}_{ddmmyyyy}.pdf
            if (!preg_match('/^(\d+)_(\d+)_(\d{8})\.pdf$/', $fileName, $m)) {
                continue;
            }

            [, $codClient, $nrFactura, $dataRaw] = $m;

            $zi       = substr($dataRaw, 0, 2);
            $luna_pdf = (int) substr($dataRaw, 2, 2);
            $an_pdf   = (int) substr($dataRaw, 4, 4);

            // Filtrează după luna/an selectat
            if ($luna_pdf !== $luna || $an_pdf !== $an) {
                continue;
            }

            $dataEmitere = $zi . '.' . str_pad($luna_pdf, 2, '0', STR_PAD_LEFT) . '.' . $an_pdf;
            $scadenta    = Carbon::createFromFormat('d.m.Y', $dataEmitere)->addDays(15)->format('d.m.Y');
            $lunaText    = Carbon::createFromFormat('d.m.Y', $dataEmitere)->locale('ro')->translatedFormat('F Y');

            if (isset($portalClients[$codClient])) {
                $c      = $portalClients[$codClient];
                $client = [
                    'client_id' => $c->client_id,
                    'nume'      => $c->name,
                    'email'     => $c->email,
                    'sursa'     => 'portal',
                ];
            } elseif (isset($externClients[$codClient])) {
                $c      = $externClients[$codClient];
                $client = [
                    'client_id' => $c->client_id,
                    'nume'      => $c->nume,
                    'email'     => $c->email,
                    'sursa'     => 'extern',
                ];
            } else {
                $client = ['client_id' => null, 'nume' => null, 'email' => null, 'sursa' => 'negasit'];
            }

            $facturi[] = [
                'cod_client'   => $codClient,
                'nr_factura'   => $nrFactura,
                'data_emitere' => $dataEmitere,
                'scadenta'     => $scadenta,
                'luna'         => $lunaText,
                'pdf_path'     => $filePath,
                'pdf_name'     => $fileName,
                'client_id'    => $client['client_id'],
                'nume'         => $client['nume'],
                'email'        => $client['email'],
                'sursa'        => $client['sursa'],
            ];
        }

        return $facturi;
    }

    /**
     * Returnează lista lunilor unice disponibile în folderul invoices
     */
    private function getLuniDisponibile(): array
    {
        $luni = [];

        // 1. Din subdirectoare an/lună — rapid, citim doar structura de directoare
        $subdirs = glob(storage_path('app/invoices/*/*'), GLOB_ONLYDIR) ?: [];
        foreach ($subdirs as $dir) {
            $parts  = explode(DIRECTORY_SEPARATOR, $dir);
            $luna_pdf = (int) end($parts);
            $an_pdf   = (int) prev($parts);

            if ($luna_pdf < 1 || $luna_pdf > 12 || $an_pdf < 2000) continue;

            // Verifică dacă există cel puțin un PDF
            $sample = glob($dir . '/*.pdf');
            if (empty($sample)) continue;

            $key = $an_pdf . '-' . str_pad($luna_pdf, 2, '0', STR_PAD_LEFT);
            if (!isset($luni[$key])) {
                $luni[$key] = [
                    'luna'  => $luna_pdf,
                    'an'    => $an_pdf,
                    'label' => Carbon::createFromDate($an_pdf, $luna_pdf, 1)->locale('ro')->translatedFormat('F Y'),
                ];
            }
        }

        // 2. Din root — citim un sample de fișiere pentru a detecta lunile
        $rootFiles = glob(storage_path('app/invoices/*.pdf')) ?: [];
        foreach ($rootFiles as $filePath) {
            $fileName = basename($filePath);
            if (!preg_match('/^\d+_\d+_(\d{8})\.pdf$/', $fileName, $m)) continue;

            $luna_pdf = (int) substr($m[1], 2, 2);
            $an_pdf   = (int) substr($m[1], 4, 4);
            $key      = $an_pdf . '-' . str_pad($luna_pdf, 2, '0', STR_PAD_LEFT);

            if (!isset($luni[$key])) {
                $luni[$key] = [
                    'luna'  => $luna_pdf,
                    'an'    => $an_pdf,
                    'label' => Carbon::createFromDate($an_pdf, $luna_pdf, 1)->locale('ro')->translatedFormat('F Y'),
                ];
            }
        }

        krsort($luni);
        return array_values($luni);
    }

    /**
     * Obține soldul din Oracle pentru un client
     */
    private function getSoldPentruClient(string $codClient, ?string $clientId): float
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
}
