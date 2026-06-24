<?php

namespace App\Http\Controllers\Cititor;

use App\Http\Controllers\Controller;
use App\Models\CitireContor;
use App\Models\Abonat;
use App\Models\RutaCitire;
use App\Services\CitiriOracleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Exports\CitiriExport;
use App\Models\StradaGeocodata;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class SupervisorController extends Controller
{
    public function __construct(
        private CitiriOracleService $oracle
    ) {}

    public function index(Request $request)
    {
        $luna   = $request->get('luna', now()->month);
        $an     = $request->get('an', now()->year);
        $ruta   = $request->get('ruta');
        $status = $request->get('status');

        $query = CitireContor::with('cititor')
            ->where('luna', $luna)
            ->where('an', $an)
            ->orderBy('ruta')
            ->orderBy('created_at', 'desc');

        if ($ruta)   $query->where('ruta', $ruta);
        if ($status) $query->where('status', $status);

        // Filtru consum suspect
        $pragConsum = request('prag_consum');
        if ($pragConsum) {
            $query->whereRaw('(index_citit - index_vechi) > ?', [$pragConsum]);
        }

        $citiri = $query->paginate(50);

        $stats = CitireContor::where('luna', $luna)
            ->where('an', $an)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $rute = RutaCitire::active()->orderBy('nume')->pluck('nume');

        $cititoriPerRuta = User::role('cititor')
            ->whereNotNull('ruta')
            ->where('name', 'NOT LIKE', 'Cititor %')
            ->pluck('name', 'ruta');

        $statisticiRute = [];
        $ruteActive = RutaCitire::active()->orderBy('nume')->get();
        foreach ($ruteActive as $r) {
            $cacheKey  = "contoare_{$r->nume}_{$luna}_{$an}";
            $contoare  = Cache::get($cacheKey, []);
            $total     = count($contoare);

            $citiriExistenteIds = CitireContor::where('luna', $luna)->where('an', $an)
                ->where('ruta', $r->nume)
                ->pluck('id_cit')->flip();

            $citate     = $citiriExistenteIds->count();
            $altundeva  = 0;
            foreach ($contoare as $c) {
                if (!empty($c['index_nou']) && !isset($citiriExistenteIds[$c['id_cit']])) {
                    $altundeva++;
                }
            }

            $statisticiRute[$r->nume] = [
                'total'     => $total,
                'citate'    => $citate,
                'altundeva' => $altundeva,
                'necitate'  => $total - $citate - $altundeva,
            ];
        }

        $coduriAbonati = $citiri->pluck('cod_abonat')->unique();
        $abonati = Abonat::whereIn('cod_abonat', $coduriAbonati)->get()->keyBy(fn($a) => $a->cod_abonat . '_' . $a->id_locatie);

        return view('cititor.supervisor.index', compact(
            'citiri', 'stats', 'rute', 'luna', 'an', 'ruta', 'status', 'statisticiRute', 'abonati'
        ));
    }

    public function harta(Request $request)
    {
        $luna = $request->get('luna', now()->month);
        $an   = $request->get('an', now()->year);
        $ruteSelectate = $request->get('ruta', []);
        if (!is_array($ruteSelectate)) $ruteSelectate = $ruteSelectate ? [$ruteSelectate] : [];

        $query = CitireContor::with(['cititor', 'supervisor'])
            ->where('luna', $luna)
            ->where('an', $an)
            ->whereNotNull('gps_lat')
            ->whereNotNull('gps_lng');

        if (count($ruteSelectate) > 0) $query->whereIn("ruta", $ruteSelectate);

        $citiri = $query->get()->map(function($c) {
            $abonat = Abonat::where('cod_abonat', $c->cod_abonat)->where('id_locatie', $c->id_locatie)->first();
            return [
                'lat'         => (float)$c->gps_lat,
                'lng'         => (float)$c->gps_lng,
                'status'      => $c->status,
                'cod_abonat'  => $c->cod_abonat,
                'nume'        => $abonat?->nume_abonat ?? $c->cod_abonat,
                'strada'      => ($abonat?->strada ?? '') . ', Nr. ' . ($abonat?->nr_strada ?? ''),
                'index_citit' => $c->index_citit,
                'consum'      => $c->consum,
                'cititor'     => $c->cititor?->name ?? '—',
                'ruta'        => $c->ruta,
                'data_citire' => $c->created_at?->format('d.m.Y H:i'),
            ];
        });

        $strazi = StradaGeocodata::when(!empty($ruteSelectate), fn($q) => $q->whereIn('ruta', $ruteSelectate))
            ->get()
            ->map(function($s) {
                return [
                    'ruta'    => $s->ruta,
                    'strada'  => $s->nume_strada,
                    'geojson' => json_decode($s->geojson, true),
                ];
            });

        if ($request->wantsJson()) {
            return response()->json(['citiri' => $citiri, 'strazi' => $strazi]);
        }

        $rute = RutaCitire::active()->orderBy('nume')->pluck('nume');

        $cititoriPerRuta = User::role('cititor')
            ->whereNotNull('ruta')
            ->where('name', 'NOT LIKE', 'Cititor %')
            ->pluck('name', 'ruta');

        return view('cititor.supervisor.harta', compact('citiri', 'luna', 'an', 'ruteSelectate', 'rute', 'strazi', 'cititoriPerRuta'));
    }

    public function show(CitireContor $citire)
    {
        $abonat = Abonat::where('cod_abonat', $citire->cod_abonat)->where('id_locatie', $citire->id_locatie)->first();
        return view('cititor.supervisor.show', compact('citire', 'abonat'));
    }

    public function confirmaBloc(Request $request)
    {
        $request->validate([
            'citiri_ids'   => 'required|array|min:1',
            'citiri_ids.*' => 'integer|exists:citiri_contoare,id',
        ]);

        $succes = 0;
        $erori  = 0;
        $mesaje = [];

        $citiri = CitireContor::whereIn('id', $request->citiri_ids)
            ->where('status', 'nou')
            ->get();

        foreach ($citiri as $citire) {
            $citire->load('cititor');
            $rezultat = $this->oracle->postIndex(
                codClient:   $citire->cod_abonat,
                codContor:   $citire->cod_contor,
                luna:        $citire->luna,
                an:          $citire->an,
                indexNou:    $citire->index_citit,
                idCit:       $citire->id_cit,
                idContor:    $citire->id_contor ?? 0,
                idLocatie:   $citire->id_locatie ?? 0,
                serieContor: $citire->serie_contor ?? '',
                codSector:   $citire->sector ?? '',
                lat:         $citire->gps_lat,
                lng:         $citire->gps_lng,
                marca:       $citire->cititor->marca ?? '',
                observatii:  $citire->observatii,
                ruta:        $citire->ruta,
            );

            if ($rezultat['success']) {
                $citire->update([
                    'status'       => 'confirmat',
                    'mesaj_oracle' => null,
                    'confirmat_de' => Auth::id(),
                    'confirmat_la' => now(),
                ]);
                $succes++;
            } else {
                $citire->update([
                    'status'       => 'eroare',
                    'mesaj_oracle' => $rezultat['mesaj'],
                ]);
                $erori++;
                $mesaje[] = "{$citire->cod_abonat}: {$rezultat['mesaj']}";
            }
        }

        $msg = "Confirmate: {$succes}";
        if ($erori > 0) {
            $msg .= ", Erori: {$erori} — " . implode('; ', array_slice($mesaje, 0, 3));
        }

        return back()->with($erori > 0 ? 'warning' : 'success', $msg);
    }

    public function confirma(Request $request, CitireContor $citire)
    {
        $request->validate(['index_citit' => 'required|integer|min:0']);

        $citire->load('cititor');
        $rezultat = $this->oracle->postIndex(
            codClient:   $citire->cod_abonat,
            codContor:   $citire->cod_contor,
            luna:        $citire->luna,
            an:          $citire->an,
            indexNou:    $request->index_citit,
            idCit:       $citire->id_cit,
            idContor:    $citire->id_contor ?? 0,
            idLocatie:   $citire->id_locatie ?? 0,
            serieContor: $citire->serie_contor ?? '',
            codSector:   $citire->sector ?? '',
            lat:         $citire->gps_lat,
            lng:         $citire->gps_lng,
            marca:       $citire->cititor->marca ?? '',
            observatii:  $citire->observatii,
                ruta:        $citire->ruta,
        );

        if ($rezultat['success']) {
            $citire->update([
                'index_citit'  => $request->index_citit,
                'status'       => 'confirmat',
                'mesaj_oracle' => null,
                'confirmat_de' => Auth::id(),
                'confirmat_la' => now(),
            ]);
            return back()->with('success', "Citirea pentru {$citire->cod_abonat} a fost confirmată în Oracle.");
        }

        $citire->update(['status' => 'eroare', 'mesaj_oracle' => $rezultat['mesaj']]);
        return back()->with('error', "Eroare Oracle: {$rezultat['mesaj']}");
    }

    public function eroare(Request $request, CitireContor $citire)
    {
        $request->validate(['observatii' => 'required|string|max:500']);

        $citire->update([
            'status'       => 'eroare',
            'observatii'   => $request->observatii,
            'confirmat_de' => Auth::id(),
            'confirmat_la' => now(),
        ]);

        return back()->with('warning', "Citirea marcată ca eroare.");
    }

    public function respinge(Request $request, CitireContor $citire)
    {
        $request->validate(['observatii' => 'required|string|max:500']);

        $citire->update([
            'status'       => 'respins',
            'observatii'   => $request->observatii,
            'confirmat_de' => Auth::id(),
            'confirmat_la' => now(),
        ]);

        return back()->with('warning', "Citirea a fost respinsă și retrimisă cititorului.");
    }

    public function statistici(Request $request)
    {
        $luna = $request->get('luna', now()->month);
        $an   = $request->get('an', now()->year);

        $cititoriPerRuta = User::role('cititor')->whereNotNull('ruta')->where('name', 'NOT LIKE', 'Cititor %')->pluck('name', 'ruta');
        $ruteActive = RutaCitire::active()->orderBy('nume')->get();
        $statistici = [];

        foreach ($ruteActive as $r) {
            $cacheKey = "contoare_{$r->nume}_{$luna}_{$an}";
            $contoare = Cache::get($cacheKey, []);
            $total    = count($contoare);
            $citiri   = CitireContor::where("luna", $luna)->where("an", $an)->where("ruta", $r->nume);
            $citiriExistenteIds = (clone $citiri)->pluck("id_cit")->flip();
            $altundeva = 0;
            foreach ($contoare as $c) {
                if (!empty($c["index_nou"]) && !isset($citiriExistenteIds[$c["id_cit"]])) {
                    $altundeva++;
                }
            }
            $statistici[] = [
                "cititor"    => $cititoriPerRuta[$r->nume] ?? "—",
                "ruta"       => $r->nume,
                "total"      => $total,
                "nou"        => (clone $citiri)->where("status", "nou")->count(),
                "confirmat"  => (clone $citiri)->where("status", "confirmat")->count(),
                "eroare"     => (clone $citiri)->where("status", "eroare")->count(),
                "respins"    => (clone $citiri)->where("status", "respins")->count(),
                "expirat"    => (clone $citiri)->where("status", "expirat")->count(),
                "altundeva"  => $altundeva,
                "necitit"    => $total - (clone $citiri)->count() - $altundeva,
            ];
        }

        return view('cititor.supervisor.statistici', compact('statistici', 'luna', 'an'));
    }

    public function export(int $luna, int $an)
    {
        $ruta   = request('ruta');
        $status = request('status', 'confirmat');
        $filename = "citiri_{$luna}_{$an}" . ($ruta ? "_{$ruta}" : '') . ".xlsx";

        return Excel::download(new CitiriExport($luna, $an, $ruta, $status), $filename);
    }
}
