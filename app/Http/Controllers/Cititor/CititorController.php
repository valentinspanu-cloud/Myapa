<?php

namespace App\Http\Controllers\Cititor;

use App\Http\Controllers\Controller;
use App\Models\CitireContor;
use App\Models\Abonat;
use App\Services\CitiriOracleService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CititorController extends Controller
{
    public function __construct(
        private CitiriOracleService $oracle
    ) {}

    public function selectieLuna()
    {
        $user = Auth::user();
        if (!$user->ruta) {
            return view('cititor.no-ruta');
        }

        $lunaAuto = now()->day >= 21 ? now()->addMonthNoOverflow()->month : now()->month;
        $anAuto   = now()->day >= 21 ? now()->addMonthNoOverflow()->year  : now()->year;

        // Genereaza ultimele 6 luni + luna curenta de citire
        $optiuni = [];
        for ($i = 0; $i <= 5; $i++) {
            $ref = now()->day >= 21
                ? now()->addMonthNoOverflow()->subMonths($i)
                : now()->subMonths($i);
            $optiuni[] = [
                'luna'  => $ref->month,
                'an'    => $ref->year,
                'label' => $ref->locale('ro')->isoFormat('MMMM YYYY'),
                'cached' => \Cache::has("contoare_{$user->ruta}_{$ref->month}_{$ref->year}"),
            ];
        }

        return view('cititor.selectie-luna', compact('lunaAuto', 'anAuto', 'optiuni'));
    }

    public function index()
    {
        $user = Auth::user();
        $ruta = $user->ruta;

        if (!$ruta) {
            return view('cititor.no-ruta');
        }

        $lunaAuto = now()->day >= 21 ? now()->addMonthNoOverflow()->month : now()->month;
        $anAuto   = now()->day >= 21 ? now()->addMonthNoOverflow()->year  : now()->year;

        $luna = (int) request('luna', $lunaAuto);
        $an   = (int) request('an',   $anAuto);

        // Blocare viitor
        if ($an > now()->year || ($an == now()->year && $luna > now()->month)) {
            $luna = $lunaAuto;
            $an   = $anAuto;
        }

        $esteLunaHistorica = ($luna !== $lunaAuto || $an !== $anAuto);

        // Cache contoare — Laravel Cache (persistent intre sesiuni)
        $cacheKey = "contoare_{$ruta}_{$luna}_{$an}";
        $contoare  = !request('refresh') ? Cache::get($cacheKey) : null;
        if (!$contoare) {
            $contoare = $this->oracle->getCitiri($ruta, $luna, $an);
            usort($contoare, function($a, $b) {
                $sA = strtolower($a['strada'] ?? '');
                $sB = strtolower($b['strada'] ?? '');
                if ($sA !== $sB) return strcmp($sA, $sB);
                return (int)($a['nr_strada'] ?? 0) - (int)($b['nr_strada'] ?? 0);
            });
            Cache::put($cacheKey, $contoare, now()->addHours(12));
        }

        // Datele locale abonati
        $coduri  = collect($contoare)->pluck('cod_abonat')->unique();
        $abonati = Abonat::whereIn('cod_abonat', $coduri)->get()->keyBy(fn($a) => $a->cod_abonat . '_' . $a->id_locatie);

        // ID-urile citite
        $citate = CitireContor::where('user_id', $user->id)
            ->where('luna', $luna)
            ->where('an', $an)
            ->where('ruta', $ruta)
            ->pluck('id_cit')
            ->toArray();

        // Citirile existente indexate
        $citiriExistente = CitireContor::where('user_id', $user->id)
            ->where('luna', $luna)
            ->where('an', $an)
            ->where('ruta', $ruta)
            ->get()
            ->keyBy('id_cit');

        // Solduri din Laravel Cache
        $solduriKey = "solduri_{$ruta}_{$luna}_{$an}";
        $solduri    = !request('refresh') ? Cache::get($solduriKey, []) : [];
        if (empty($solduri)) {
            foreach ($contoare as $contor) {
                $idClient = $contor['id_client'];
                if (!isset($solduri[$idClient])) {
                    $solduri[$idClient] = $this->oracle->getSold($idClient);
                }
            }
            Cache::put($solduriKey, $solduri, now()->addHours(4));
        }

        // Observatii din ultimele 6 luni — indexate dupa cod_abonat
        // Structura: [cod_abonat => [[luna, an, obs], ...]]
        $ref6 = now()->day >= 21 ? now()->addMonthNoOverflow() : now();
        $perioade = [];
        for ($i = 1; $i <= 6; $i++) {
            $p = $ref6->copy()->subMonths($i);
            $perioade[] = ['luna' => $p->month, 'an' => $p->year];
        }
        $observatiiAnt = [];
        foreach ($perioade as $p) {
            $rows = CitireContor::where('luna', $p['luna'])
                ->where('an', $p['an'])
                ->where('ruta', $ruta)
                ->whereNotNull('observatii')
                ->where('observatii', '!=', '')
                ->get(['cod_abonat', 'observatii', 'luna', 'an']);
            foreach ($rows as $row) {
                $observatiiAnt[$row->cod_abonat][] = [
                    'luna'      => $row->luna,
                    'an'        => $row->an,
                    'observatii' => $row->observatii,
                ];
            }
        }

        // Marcam contoarele cu index deja introdus din alta sursa (nu de noi)
        foreach ($contoare as &$contor) {
            $idCit = $contor['id_cit'];
            $jaIntrodusAltundeva = !empty($contor['index_nou']) && !isset($citiriExistente[$idCit]);
            $contor['index_deja_introdus'] = $jaIntrodusAltundeva;
        }
        unset($contor);

        return view('cititor.index', compact(
            'contoare', 'abonati', 'citate', 'citiriExistente', 'solduri',
            'ruta', 'luna', 'an', 'lunaAuto', 'anAuto', 'esteLunaHistorica',
            'observatiiAnt'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cit'           => 'required|integer',
            'cod_abonat'       => 'required|string',
            'id_client'        => 'required|integer',
            'id_locatie'       => 'nullable|integer',
            'id_contor'        => 'nullable|integer',
            'sector'           => 'nullable|string',
            'ruta'             => 'required|string',
            'luna'             => 'required|integer|min:1|max:12',
            'an'               => 'required|integer|min:2020',
            'serie_contor'     => 'nullable|string',
            'cod_contor'       => 'nullable|string',
            'tip_contor'       => 'nullable|string',
            'index_vechi'      => 'nullable|integer',
            'index_nou_oracle' => 'nullable|integer',
            'index_citit'      => 'required|integer|min:0',
            'gps_lat'          => 'nullable|numeric',
            'gps_lng'          => 'nullable|numeric',
            'observatii'       => 'nullable|string|max:500',
            'foto'             => 'nullable|image|max:8192',
        ]);

        $user = Auth::user();

        $exista = CitireContor::where('id_cit', $request->id_cit)
            ->where('luna', $request->luna)
            ->where('an', $request->an)
            ->first();

        if ($exista) {
            return back()->with('warning', 'Acest contor a fost deja citit în această perioadă.');
        }

        $sold = $this->oracle->getSold($request->id_client);

        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $dataOra  = now()->format('Ymd_His');
            $filename = "{$request->cod_abonat}_{$dataOra}.jpg";
            $folder   = "citiri/{$request->cod_abonat}/" . now()->format('Y-m');
            Storage::disk("local")->makeDirectory($folder);
            $fotoPath = $request->file('foto')->storeAs($folder, $filename, 'local');
        }

        CitireContor::create([
            'user_id'          => $user->id,
            'id_cit'           => $request->id_cit,
            'cod_abonat'       => $request->cod_abonat,
            'id_client'        => $request->id_client,
            'id_locatie'       => $request->id_locatie,
            'id_contor'        => $request->id_contor,
            'sector'           => $request->sector,
            'ruta'             => $request->ruta,
            'luna'             => $request->luna,
            'an'               => $request->an,
            'serie_contor'     => $request->serie_contor,
            'cod_contor'       => $request->cod_contor,
            'tip_contor'       => $request->tip_contor,
            'index_vechi'      => $request->index_vechi,
            'index_nou_oracle' => $request->index_nou_oracle,
            'index_citit'      => $request->index_citit,
            'sold_moment'      => $sold,
            'foto_path'        => $fotoPath,
            'gps_lat'          => $request->gps_lat,
            'gps_lng'          => $request->gps_lng,
            'observatii'       => $request->observatii,
            'status'           => 'nou',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Citire salvată pentru {$request->cod_abonat}."]);
        }
        return back()->with('success', "Citire salvată pentru {$request->cod_abonat}.");
    }

    public function show(int $idCit)
    {
        $citire = CitireContor::where('id_cit', $idCit)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return view('cititor.show', compact('citire'));
    }

    public function detaliu(Request $request, int $idClient, int $idCit)
    {
        $sold   = $this->oracle->getSold($idClient);
        $citire = CitireContor::where('id_cit', $idCit)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'sold'        => $sold,
            'index_citit' => $citire?->index_citit,
            'status'      => $citire?->status,
            'citire_id'   => $citire?->id,
            'observatii'   => $citire?->observatii,
            'mesaj_oracle' => $citire?->mesaj_oracle,
            'foto_path'   => $citire?->foto_path,
        ]);
    }

    public function edit(CitireContor $citire)
    {
        abort_if($citire->user_id !== Auth::id(), 403);
        abort_if(!in_array($citire->status, ['nou', 'eroare', 'respins']), 403, 'Citirea a fost deja confirmată.');
        return view('cititor.edit', compact('citire'));
    }

    public function update(Request $request, CitireContor $citire)
    {
        abort_if($citire->user_id !== Auth::id(), 403);
        abort_if(!in_array($citire->status, ['nou', 'eroare', 'respins']), 403, 'Citirea a fost deja confirmată.');
        $request->validate([
            'index_citit' => 'required|integer|min:0',
            'gps_lat'     => 'nullable|numeric',
            'gps_lng'     => 'nullable|numeric',
            'observatii'  => 'nullable|string|max:500',
            'foto'        => 'nullable|image|max:8192',
        ]);

        $data = [
            'status'      => 'nou',
            'index_citit' => $request->index_citit,
            'observatii'  => $request->observatii,
        ];

        if ($request->gps_lat && $request->gps_lng) {
            $data['gps_lat'] = $request->gps_lat;
            $data['gps_lng'] = $request->gps_lng;
        }
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($citire->foto_path) {
                Storage::disk('local')->delete($citire->foto_path);
            }
            $dataOra  = now()->format('Ymd_His');
            $filename = "{$citire->cod_abonat}_{$dataOra}.jpg";
            $folder   = "citiri/{$citire->cod_abonat}/" . now()->format('Y-m');
            Storage::disk("local")->makeDirectory($folder);
            $data['foto_path'] = $request->file('foto')->storeAs($folder, $filename, 'local');
        }

        $citire->update($data);

        return redirect()->route('cititor.index')
            ->with('success', "Citirea pentru {$citire->cod_abonat} a fost actualizată.");
    }
}
