@extends('cititor.layout')
@section('title', 'Gestiune Rute')
@section('header_title', 'Rute Citire')

@section('content')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-xl text-sm mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('sync_output'))
<div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-xs mb-4 font-mono whitespace-pre-wrap">
{{ session('sync_output') }}
</div>
@endif

{{-- Butoane sincronizare --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <form method="POST" action="{{ route('cititor.supervisor.rute.sync') }}">
        @csrf
        <button type="submit"
            onclick="return confirm('Sincronizează rutele din Oracle? Poate dura câteva minute.')"
            class="w-full bg-[#1e3a5f] text-white px-3 py-3 rounded-xl text-sm font-medium">
            🔄 Sync Rute Oracle
        </button>
    </form>
    <form method="POST" action="{{ route('cititor.supervisor.rute.sync-contoare') }}">
        @csrf
        <button type="submit"
            onclick="return confirm('Sincronizează toate contoarele? Va dura câteva minute.')"
            class="w-full bg-blue-600 text-white px-3 py-3 rounded-xl text-sm font-medium">
            🔄 Sync Contoare
        </button>
    </form>
</div>

{{-- Adauga ruta noua --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <div class="text-xs font-medium text-gray-600 mb-2">Adaugă rută nouă</div>
    <form method="POST" action="{{ route('cititor.supervisor.rute.store') }}" class="flex gap-2">
        @csrf
        <input type="text" name="nume" placeholder="ex: TULCEA12"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-[#1e3a5f]">
        <button type="submit" class="bg-[#1e3a5f] text-white px-4 py-2 rounded-lg text-sm font-medium">
            Adaugă
        </button>
    </form>
    @error('nume')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>

{{-- Lista rute --}}
<div class="space-y-2">
@forelse($rute as $ruta)
<div class="bg-white rounded-xl shadow-sm px-4 py-3 flex items-center justify-between {{ !$ruta->activa ? 'opacity-50' : '' }}">
    <div>
        <div class="font-semibold text-[#1e3a5f]">{{ $ruta->nume }}</div>
        <div class="text-xs text-gray-500 mt-0.5">
            @if($ruta->activa)
                <span class="text-green-600">● Activă</span>
            @else
                <span class="text-red-500">● Inactivă</span>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ route('cititor.supervisor.rute.toggle', $ruta) }}">
        @csrf
        <button type="submit"
            class="text-xs {{ $ruta->activa ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} px-3 py-1.5 rounded-lg font-medium">
            {{ $ruta->activa ? 'Dezactivează' : 'Activează' }}
        </button>
    </form>
</div>
@empty
<div class="text-center py-8 text-gray-500 text-sm">
    Nicio rută. Apasă "Sync Rute Oracle" pentru a importa.
</div>
@endforelse
</div>

<div class="mt-6">
    <a href="{{ route('cititor.supervisor.index') }}"
        class="text-[#1e3a5f] text-sm font-medium">← Înapoi la citiri</a>
</div>

@endsection
