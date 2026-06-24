<?php

namespace App\Http\Controllers\Cititor;

use App\Http\Controllers\Controller;
use App\Models\RutaCitire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

    public function store(Request $request)
    {
        $request->validate([
            'nume' => 'required|string|uppercase|unique:rute_citire,nume',
        ]);

        RutaCitire::create(['nume' => strtoupper($request->nume), 'activa' => true]);

        return back()->with('success', "Ruta {$request->nume} adăugată.");
    }
}
