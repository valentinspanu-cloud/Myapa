<?php

namespace App\Http\Controllers\Cititor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RutaCitire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupervisorCititoriController extends Controller
{
    public function index()
    {
        $cititori = User::role('cititor')
            ->orderBy('ruta')
            ->orderBy('name')
            ->get();

        $rute = RutaCitire::active()->orderBy('nume')->get();

        return view('cititor.supervisor.cititori.index', compact('cititori', 'rute'));
    }

    public function create()
    {
        $rute = RutaCitire::active()->orderBy('nume')->get();
        return view('cititor.supervisor.cititori.create', compact('rute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'ruta'  => 'required|exists:rute_citire,nume',
        ]);

        $parola = 'Schimba@' . now()->year . '!';

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($parola),
            'ruta'              => $request->ruta,
            'status'            => 1,
            'notify'            => 0,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('cititor');

        return redirect()->route('cititor.supervisor.cititori.index')
            ->with('success', "Cititor creat: {$user->name} — parolă temporară: {$parola}");
    }

    public function edit(User $user)
    {
        $rute = RutaCitire::active()->orderBy('nume')->get();
        return view('cititor.supervisor.cititori.edit', compact('user', 'rute'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'ruta'  => 'required|exists:rute_citire,nume',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'ruta'  => $request->ruta,
        ]);

        return redirect()->route('cititor.supervisor.cititori.index')
            ->with('success', "Cititor actualizat: {$user->name}");
    }

    public function toggleStatus(User $user)
    {
        $status = $user->status == 1 ? 0 : 1;
        $user->update(['status' => $status]);

        $mesaj = $status == 1 ? 'activat' : 'dezactivat';
        return back()->with('success', "Cititor {$mesaj}: {$user->name}");
    }

    public function resetParola(User $user)
    {
        $parola = 'Schimba@' . now()->year . '!';
        $user->update(['password' => Hash::make($parola)]);

        return back()->with('success', "Parolă resetată pentru {$user->name}: {$parola}");
    }
}
