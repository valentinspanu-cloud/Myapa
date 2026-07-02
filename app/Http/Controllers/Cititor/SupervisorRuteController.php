<?php

namespace App\Http\Controllers\Cititor;

use App\Http\Controllers\Controller;
use App\Models\RutaCitire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\CitiriOracleService;

class SupervisorRuteController extends Controller
{
    public function index()
    {
        $rute = RutaCitire::orderBy('nume')->get();
        return view('cititor.supervisor.rute.index', compact('rute'));
    }

    public function sync()
    {
        dispatch(function() {
            Artisan::call('cititori:sync-rute', ['--max' => 30]);
        })->afterResponse();

        return back()->with('success', 'Sincronizare rute pornită în background. Va dura câteva minute.');
    }

    public function syncContoare()
    {
        set_time_limit(300);
        Artisan::call('cititori:sync');
        return back()->with('success', 'Sincronizare contoare finalizată!');
    }

    public function toggleActiva(RutaCitire $ruta)
    {
        $ruta->update(['activa' => !$ruta->activa]);
        $stare = $ruta->activa ? 'activată' : 'dezactivată';
        return back()->with('success', "Ruta {$ruta->nume} {$stare}.");
    }

    public function syncContoareAsync(Request $request)
    {
        $luna = now()->day >= 21 ? now()->addMonthNoOverflow()->month : now()->month;
        $an   = now()->day >= 21 ? now()->addMonthNoOverflow()->year  : now()->year;
        $ruta = $request->get('ruta');

        $oracle = app(CitiriOracleService::class);
        $contoare = $oracle->getCitiri($ruta, $luna, $an);
        usort($contoare, function($a, $b) {
            $sA = strtolower($a['strada'] ?? '');
            $sB = strtolower($b['strada'] ?? '');
            if ($sA !== $sB) return strcmp($sA, $sB);
            return (int)($a['nr_strada'] ?? 0) - (int)($b['nr_strada'] ?? 0);
        });
        Cache::put("contoare_{$ruta}_{$luna}_{$an}", $contoare, now()->addHours(12));

        $solduri = [];
        foreach ($contoare as $contor) {
            $idClient = $contor['id_client'];
            if (!isset($solduri[$idClient])) {
                $solduri[$idClient] = $oracle->getSold($idClient);
            }
        }
        Cache::put("solduri_{$ruta}_{$luna}_{$an}", $solduri, now()->addHours(4));

        return response()->json([
            'success'   => true,
            'ruta'      => $ruta,
            'contoare'  => count($contoare),
            'solduri'   => count($solduri),
            'luna'      => $luna,
            'an'        => $an,
        ]);
    }

    public function syncStatus()
    {
        $luna = now()->day >= 21 ? now()->addMonthNoOverflow()->month : now()->month;
        $an   = now()->day >= 21 ? now()->addMonthNoOverflow()->year  : now()->year;
        $rute = RutaCitire::where('activa', true)->orderBy('nume')->get();
        $status = [];
        foreach ($rute as $r) {
            $cached = Cache::has("contoare_{$r->nume}_{$luna}_{$an}");
            $contoare = $cached ? count(Cache::get("contoare_{$r->nume}_{$luna}_{$an}", [])) : 0;
            $status[] = [
                'ruta'     => $r->nume,
                'cached'   => $cached,
                'contoare' => $contoare,
            ];
        }
        return response()->json(['status' => $status, 'luna' => $luna, 'an' => $an]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nume' => 'required|string|uppercase|unique:rute_citire,nume',
        ]);

        RutaCitire::create(['nume' => strtoupper($request->nume), 'activa' => true]);

        return back()->with('success', "Ruta {$request->nume} adăugată.");
    }
}
