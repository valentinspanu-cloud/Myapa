<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CitiriOracleService
{
    private string $baseUrl;    // http://192.168.1.45:2125/ords/svap/op/
    private string $citUrl;     // http://192.168.1.45:2125/ords/svap/cit/
    private int $timeout;

    public function __construct()
    {
        // SIVAPPS_URL = http://192.168.1.45:2125/ords/svap/op/
        $opUrl = rtrim(env('SIVAPPS_URL'), '/');
        $this->baseUrl = $opUrl;

        // Derivam URL-ul pentru modulul cit/ din acelasi host
        // http://192.168.1.45:2125/ords/svap/op/ -> http://192.168.1.45:2125/ords/svap/cit/
        $this->citUrl  = preg_replace('/\/op\/?$/', '/cit', $opUrl);

        $this->timeout = 15;
    }

    /**
     * Aduce lista de contoare pentru o ruta si o perioada.
     * Pagineaza automat pana la hasMore = false.
     */
    public function getCitiri(string $ruta, int $luna, int $an): array
    {
        $items   = [];
        $id_cit  = 1;
        $hasMore = true;

        while ($hasMore) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->citUrl}/citiri", [
                        'ruta'   => $ruta,
                        'luna'   => $luna,
                        'an'     => $an,
                        'id_cit' => $id_cit,
                    ]);

                if (!$response->successful()) {
                    Log::error('CitiriOracleService::getCitiri eroare HTTP', [
                        'status' => $response->status(),
                        'ruta'   => $ruta,
                    ]);
                    break;
                }

                $data    = $response->json();
                
                $batch   = $data['items'] ?? [];
                $items   = array_merge($items, $batch);
                $hasMore = count($batch) >= 20;

                if ($hasMore && !empty($batch)) {
                    $id_cit = end($batch)['id_cit'] + 1;
                }

            } catch (\Exception $e) {
                Log::error('CitiriOracleService::getCitiri exceptie', [
                    'message' => $e->getMessage(),
                    'ruta'    => $ruta,
                ]);
                break;
            }
        }

        return $items;
    }

    /**
     * Aduce soldul unui client din Oracle.
     * Acelasi endpoint ca portalul MyAPA — SIVAPPS_URL/sold
     */
    public function getSold(int $idClient): float|null
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/sold", [
                    'idfirma'  => $idClient,
                    'datasold' => now()->format('d-M-Y'),
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            return $data['items'][0]['sold'] ?? null;

        } catch (\Exception $e) {
            Log::error('CitiriOracleService::getSold exceptie', [
                'message'   => $e->getMessage(),
                'id_client' => $idClient,
            ]);
            return null;
        }
    }

    /**
     * Trimite indexul cititorului de teren catre Oracle via POST /cit/citiri_realizate.
     */
    public function postIndex(
        string  $codClient,
        string  $codContor,
        int     $luna,
        int     $an,
        int     $indexNou,
        int     $idCit      = 0,
        int     $idContor   = 0,
        int     $idLocatie  = 0,
        string  $serieContor = '',
        string  $codSector  = '',
        ?float  $lat        = null,
        ?float  $lng        = null,
        string  $marca      = '',
        ?string $observatii = null,
        string  $ruta       = ''
    ): array {
        try {
            $dataCitire = now()->format('d/m/Y');

            // Terminal: CIT_TL{numar_ruta}, ex: TULCEA2 -> CIT_TL2
            preg_match('/(\d+)$/', $ruta, $matches);
            $numarRuta = $matches[1] ?? '';
            $terminal  = $numarRuta !== '' ? substr('CIT_TL' . $numarRuta, 0, 10) : 'CIT';

            $response = Http::timeout($this->timeout)
                ->post("{$this->citUrl}/citiri_realizate", [
                    'an'                => $an,
                    'luna'              => $luna,
                    'id_contor'         => $idContor,
                    'id_locatie'        => $idLocatie,
                    'cod_sector'        => $codSector,
                    'cod_contor'        => $codContor,
                    'serie_contor'      => $serieContor,
                    'cod_abonat'        => $codClient,
                    'valoare_index_nou' => $indexNou,
                    'data_citire'       => $dataCitire,
                    'id_tip_provenienta'=> 3,
                    'latitudine'        => $lat,
                    'longitudine'       => $lng,
                    'marca'             => $marca,
                    'id_cit'            => $idCit,
                    'observatii'        => $observatii,
                    'terminal'          => $terminal,
                ]);

            // Oracle returneaza 200 cu body gol = succes
            if ($response->successful()) {
                $body = $response->body();
                if (empty(trim($body))) {
                    return ['success' => true, 'mesaj' => null];
                }
                $data = $response->json();
                if (isset($data['code'])) {
                    return ['success' => false, 'mesaj' => $data['message'] ?? 'Eroare Oracle'];
                }
                return ['success' => true, 'mesaj' => null];
            }

            return ['success' => false, 'mesaj' => 'Eroare HTTP: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('CitiriOracleService::postIndex exceptie', [
                'message'    => $e->getMessage(),
                'cod_client' => $codClient,
            ]);
            return ['success' => false, 'mesaj' => $e->getMessage()];
        }
    }
}
