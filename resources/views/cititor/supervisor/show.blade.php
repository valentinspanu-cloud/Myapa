@extends('cititor.layout')
@section('title', 'Citire ' . $citire->cod_abonat)
@section('header_title', 'Detaliu Citire')

@section('content')

{{-- Card principal --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
    <div class="px-4 py-3 bg-[#1e3a5f] text-white">
        <div class="font-semibold">{{ $abonat->nume_abonat ?? '—' }} · <span class="font-mono">{{ $citire->cod_abonat }}</span></div>
        <div class="text-xs text-blue-200">{{ $abonat->strada ?? '' }}, Nr. {{ $abonat->nr_strada ?? '' }}</div>
        <div class="text-xs text-blue-200">{{ $citire->ruta }} · {{ $citire->luna }}/{{ $citire->an }}</div>
    </div>

    <div class="p-4 grid grid-cols-2 gap-3 text-sm border-b border-gray-100">
        <div>
            <div class="text-xs text-gray-400">Serie contor</div>
            <div class="font-mono font-medium">{{ $citire->serie_contor ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Cititor</div>
            <div class="font-medium">{{ $citire->cititor->name ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Index vechi</div>
            <div class="font-semibold text-gray-700">{{ $citire->index_vechi ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Index Oracle</div>
            <div class="font-semibold text-blue-600">{{ $citire->index_nou_oracle ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Index citit</div>
            <div class="font-semibold text-lg text-[#1e3a5f]">{{ $citire->index_citit ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Consum</div>
            <div class="font-semibold text-lg {{ ($citire->consum ?? 0) > 20 ? 'text-red-600' : 'text-green-600' }}">
                {{ $citire->consum !== null ? $citire->consum . ' m³' : '—' }}
            </div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Sold la citire</div>
            <div class="font-semibold {{ ($citire->sold_moment ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $citire->sold_moment !== null ? number_format($citire->sold_moment, 2) . ' RON' : '—' }}
            </div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Data citire</div>
            <div class="font-medium">{{ $citire->created_at->format('d.m.Y H:i') }}</div>
        </div>
    </div>

    {{-- Observatii --}}
    @if($citire->observatii)
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="text-xs text-gray-400 mb-1">Observații</div>
        <div class="text-sm text-gray-700">{{ $citire->observatii }}</div>
    </div>
    @endif

    {{-- Mesaj Oracle --}}
    @if($citire->mesaj_oracle)
    <div class="px-4 py-3 border-b border-gray-100 bg-red-50">
        <div class="text-xs text-red-500 mb-1">Mesaj Oracle</div>
        <div class="text-sm text-red-700 font-mono">{{ $citire->mesaj_oracle }}</div>
    </div>
    @endif

    {{-- GPS --}}
    @if($citire->gps_lat && $citire->gps_lng)
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="text-xs text-gray-400 mb-2">Locație GPS</div>
        <a href="{{ $citire->maps_url }}" target="_blank"
            class="flex items-center gap-2 text-blue-600 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            {{ $citire->gps_lat }}, {{ $citire->gps_lng }} — Vezi pe hartă
        </a>
    </div>
    @endif

    {{-- Fotografie --}}
    @if($citire->foto_path)
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="text-xs text-gray-400 mb-2">Fotografie</div>
        <img src="{{ route('cititor.foto', str_replace('citiri/', '', $citire->foto_path)) }}"
            alt="Fotografie contor {{ $citire->cod_abonat }}"
            class="w-full rounded-lg object-contain bg-gray-100"
            style="max-height: 70vh">
    </div>
    @endif
</div>

{{-- Actiuni supervisor --}}
@if($citire->status === 'nou' || $citire->status === 'eroare')
<div class="space-y-3">

    {{-- Confirmare cu posibilitate corectie --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-semibold text-[#1e3a5f] mb-3 text-sm">Confirmare și trimitere în Oracle</h3>
        <form action="{{ route('cititor.supervisor.confirma', $citire) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Index de confirmat (corectați dacă e necesar)
                </label>
                <input type="number" name="index_citit"
                    value="{{ $citire->index_citit }}"
                    min="{{ $citire->index_vechi ?? 0 }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-3 text-xl font-mono font-bold text-center focus:ring-2 focus:ring-[#1e3a5f]">
            </div>
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition-colors"
                onclick="return confirm('Confirmați trimiterea indexului în Oracle?')">
                ✓ Confirmă și trimite în Oracle
            </button>
        </form>
    </div>

    {{-- Marcare eroare --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-semibold text-red-600 mb-3 text-sm">Marchează eroare</h3>
        <form action="{{ route('cititor.supervisor.eroare', $citire) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Motiv eroare *</label>
                <input type="text" name="observatii" required maxlength="500"
                    placeholder="ex: contor inaccesibil, index incorect..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400">
            </div>
            <button type="submit"
                class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2 rounded-xl transition-colors text-sm"
                onclick="return confirm('Marchezi această citire ca eroare?')">
                ✗ Marchează eroare
            </button>
        </form>
    </div>

    {{-- Respingere — retrimite la cititor --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-semibold text-orange-600 mb-3 text-sm">Respinge — retrimite la cititor</h3>
        <form action="{{ route('cititor.supervisor.respinge', $citire) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Motiv respingere *</label>
                <input type="text" name="observatii" required maxlength="500"
                    placeholder="ex: index prea mare, poză neclară, re-verificați..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
            </div>
            <button type="submit"
                class="w-full bg-orange-100 hover:bg-orange-200 text-orange-700 font-semibold py-2 rounded-xl transition-colors text-sm"
                onclick="return confirm('Respecția citirea la cititor?')">
                ↩ Respinge și retrimite la cititor
            </button>
        </form>
    </div>
</div>
@elseif($citire->status === 'confirmat')
<div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
    <div class="text-green-700 font-semibold">✓ Citire confirmată în Oracle</div>
    <div class="text-xs text-green-600 mt-1">
        de {{ $citire->supervisor->name ?? '—' }}
        la {{ $citire->confirmat_la?->format('d.m.Y H:i') }}
    </div>
</div>
@endif

{{-- Inapoi --}}
<a href="{{ route('cititor.supervisor.index') }}"
    class="mt-4 flex items-center gap-2 text-[#1e3a5f] text-sm font-medium py-2">
    ← Înapoi la lista
</a>

@endsection
