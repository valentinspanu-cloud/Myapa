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
    <button type="button" onclick="startSync()"
        id="btn-sync"
        class="w-full bg-blue-600 text-white px-3 py-3 rounded-xl text-sm font-medium">
        🔄 Sync Contoare
    </button>
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
@push('scripts')
<script>
const SYNC_URL   = "{{ route('cititor.supervisor.rute.sync-contoare-async') }}";
const STATUS_URL = "{{ route('cititor.supervisor.rute.sync-status') }}";
const CSRF       = "{{ csrf_token() }}";

async function startSync() {
    const statusRes  = await fetch(STATUS_URL);
    const statusData = await statusRes.json();
    const rute = statusData.status;
    const luna = statusData.luna;
    const an   = statusData.an;

    document.getElementById('sync-panel').classList.remove('hidden');
    document.getElementById('btn-sync').disabled = true;
    document.getElementById('btn-sync').textContent = '⏳ Se sincronizează...';
    document.getElementById('sync-luna').textContent = luna + '/' + an;

    const lista = document.getElementById('sync-lista');
    lista.innerHTML = '';

    rute.forEach(r => {
        const row = document.createElement('div');
        row.id = 'row_' + r.ruta;
        row.className = 'flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg text-sm';
        row.innerHTML = `
            <span class="font-medium text-gray-700">${r.ruta}</span>
            <span id="status_${r.ruta}" class="text-xs px-2 py-0.5 rounded-full ${r.cached ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'}">
                ${r.cached ? '⚡ ' + r.contoare + ' cached' : '⬜ în așteptare'}
            </span>
        `;
        lista.appendChild(row);
    });

    let ok = 0, erori = 0;
    for (const r of rute) {
        const span = document.getElementById('status_' + r.ruta);
        span.className = 'text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700';
        span.textContent = '🔄 se încarcă...';
        try {
            const res = await fetch(SYNC_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ ruta: r.ruta })
            });
            const data = await res.json();
            if (data.success) {
                span.className = 'text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700';
                span.textContent = '⚡ ' + data.contoare + ' contoare';
                ok++;
            } else { throw new Error('err'); }
        } catch(e) {
            span.className = 'text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700';
            span.textContent = '❌ eroare';
            erori++;
        }
        const pct = Math.round(((ok + erori) / rute.length) * 100);
        document.getElementById('sync-progress').style.width = pct + '%';
        document.getElementById('sync-pct').textContent = pct + '%';
    }

    document.getElementById('btn-sync').disabled = false;
    document.getElementById('btn-sync').textContent = '🔄 Sync Contoare';
    document.getElementById('sync-final').classList.remove('hidden');
    document.getElementById('sync-final').textContent = '✅ Finalizat: ' + ok + ' rute OK' + (erori ? ', ' + erori + ' erori' : '');
}
</script>
@endpush

<div id="sync-panel" class="hidden mt-4 bg-white rounded-xl shadow-sm p-4">
    <div class="flex items-center justify-between mb-2">
        <div class="text-sm font-semibold text-gray-700">Sincronizare contoare</div>
        <div class="text-xs text-gray-500">Luna: <span id="sync-luna">—</span></div>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
        <div id="sync-progress" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width:0%"></div>
    </div>
    <div class="text-right text-xs text-gray-500 mb-3" id="sync-pct">0%</div>
    <div id="sync-lista" class="space-y-1 max-h-64 overflow-y-auto"></div>
    <div id="sync-final" class="hidden mt-3 text-sm font-medium text-green-700 bg-green-50 px-3 py-2 rounded-lg"></div>
</div>
