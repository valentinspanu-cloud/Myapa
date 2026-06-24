@extends('cititor.layout')
@section('title', 'Supervisor Citiri')
@section('header_title', 'Supervisor Citiri')

@section('content')

{{-- Butoane navigare --}}
<div class="flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('cititor.supervisor.cititori.index') }}"
        class="bg-[#1e3a5f] text-white px-4 py-2 rounded-xl text-sm font-medium">
        👥 Cititori
    </a>
    <a href="{{ route('cititor.supervisor.rute.index') }}"
        class="bg-gray-100 text-[#1e3a5f] px-4 py-2 rounded-xl text-sm font-medium">
        🗺 Rute
    </a>
    <a href="{{ route('cititor.supervisor.export', [$luna, $an]) }}?ruta={{ request('ruta') }}&status={{ request('status', 'confirmat') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-medium">
        📥 Export Excel
    </a>
    <a href="{{ route('cititor.supervisor.statistici') }}?luna={{ $luna }}&an={{ $an }}"
        class="bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium">
        📊 Statistici
    </a>
</div>



{{-- Flash messages --}}
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-xl text-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('warning'))
<div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-xl text-sm mb-4">{{ session('warning') }}</div>
@endif

{{-- Statistici status --}}
<div class="grid grid-cols-4 gap-2 mb-4">
    @foreach(['nou' => ['Noi','blue'], 'confirmat' => ['OK','green'], 'eroare' => ['Erori','red'], 'corectat' => ['Corectate','yellow']] as $st => $info)
    <div class="bg-white rounded-xl shadow-sm p-3 text-center">
        <div class="text-lg font-bold text-{{ $info[1] }}-600">{{ $stats[$st] ?? 0 }}</div>
        <div class="text-xs text-gray-500">{{ $info[0] }}</div>
    </div>
    @endforeach
</div>

{{-- Filtre --}}
<form method="GET" class="bg-white rounded-xl shadow-sm p-3 mb-4 grid grid-cols-2 gap-2">
    <select name="luna" class="border border-gray-300 rounded-lg px-2 py-2 text-sm">
        @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ $luna == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
            </option>
        @endfor
    </select>
    <select name="an" class="border border-gray-300 rounded-lg px-2 py-2 text-sm">
        @foreach([now()->year, now()->year - 1] as $y)
            <option value="{{ $y }}" {{ $an == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <select name="ruta" class="border border-gray-300 rounded-lg px-2 py-2 text-sm">
        <option value="">Toate rutele</option>
        @foreach($rute as $r)
            <option value="{{ $r }}" {{ request('ruta') == $r ? 'selected' : '' }}>{{ $r }}</option>
        @endforeach
    </select>
    <select name="status" class="border border-gray-300 rounded-lg px-2 py-2 text-sm">
        <option value="">Toate statusurile</option>
        <option value="nou"       {{ request('status') == 'nou'       ? 'selected' : '' }}>Noi</option>
        <option value="confirmat" {{ request('status') == 'confirmat' ? 'selected' : '' }}>Confirmate</option>
        <option value="eroare"    {{ request('status') == 'eroare'    ? 'selected' : '' }}>Erori</option>
    </select>
    <div class="col-span-2 flex gap-2">
        <input type="number" name="prag_consum" value="{{ request('prag_consum') }}"
            placeholder="Consum > X m³ (ex: {{ getSetting('index_consum_prag') ?? 40 }})"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <button type="submit" class="bg-[#1e3a5f] text-white rounded-lg px-4 py-2 text-sm font-medium">
            Filtrează
        </button>
    </div>
</form>

{{-- Formular confirmare in bloc --}}
<form method="POST" action="{{ route('cititor.supervisor.confirma-bloc') }}" id="form-bloc">
@csrf

{{-- Select all / Confirma bloc --}}
<div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-3 flex items-center justify-between">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" id="select-all" class="w-5 h-5 rounded accent-[#1e3a5f]">
        <span class="text-sm font-medium text-[#1e3a5f]">Selectează toate</span>
    </label>
    <button type="submit" id="btn-confirma-bloc"
        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl disabled:opacity-40"
        disabled
        onclick="return confirm('Confirmi trimiterea indexurilor selectate în Oracle?')">
        ✓ Confirmă selecția
    </button>
</div>

{{-- Lista citiri --}}
<div class="space-y-2">
@forelse($citiri as $citire)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 flex items-center gap-3">

            {{-- Checkbox doar pentru status nou --}}
            @if($citire->status === 'nou')
            <input type="checkbox" name="citiri_ids[]" value="{{ $citire->id }}"
                class="citire-checkbox w-5 h-5 rounded accent-[#1e3a5f] flex-shrink-0"
                onchange="updateBlocBtn()">
            @else
            <div class="w-5 h-5 flex-shrink-0"></div>
            @endif

            <a href="{{ route('cititor.supervisor.show', $citire) }}" class="flex-1 min-w-0 flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-[#1e3a5f] text-sm truncate">
                        {{ isset($abonati[$citire->cod_abonat . '_' . $citire->id_locatie]) ? $abonati[$citire->cod_abonat . '_' . $citire->id_locatie]->nume_abonat : $citire->cod_abonat }}
                    </div>
                    <div class="text-xs text-gray-400 font-mono">{{ $citire->cod_abonat }}</div>
                    @if(isset($abonati[$citire->cod_abonat . '_' . $citire->id_locatie]))
                    <div class="text-xs text-gray-500">{{ $abonati[$citire->cod_abonat . '_' . $citire->id_locatie]->strada }}, Nr. {{ $abonati[$citire->cod_abonat . '_' . $citire->id_locatie]->nr_strada }}</div>
                    @endif
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ $citire->ruta }} · {{ $citire->cititor->name ?? '—' }}
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        Index: {{ $citire->index_vechi ?? '—' }} → <span class="font-semibold text-gray-700">{{ $citire->index_citit ?? '—' }}</span>
                        @if($citire->consum !== null)
                            <span class="text-blue-600">({{ $citire->consum }} m³)</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1 ml-3 flex-shrink-0">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        @if($citire->status === 'nou')       bg-blue-100 text-blue-700
                        @elseif($citire->status === 'confirmat') bg-green-100 text-green-700
                        @elseif($citire->status === 'eroare')    bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ ucfirst($citire->status) }}
                    </span>
                    @if($citire->foto_path)
                        <span class="text-gray-400" title="Are fotografie">📷</span>
                    @endif
                    @if($citire->gps_lat)
                        <span class="text-gray-400" title="Are GPS">📍</span>
                    @endif
                </div>
            </a>
        </div>
    </div>
@empty
    <div class="text-center py-12 text-gray-500 text-sm">
        Nicio citire pentru filtrele selectate.
    </div>
@endforelse
</div>

{{-- Paginare --}}
<div class="mt-4">
    {{ $citiri->appends(request()->query())->links() }}
</div>

</form>

@endsection

@push('scripts')
<script>
// Select all
document.getElementById('select-all').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.citire-checkbox').forEach(cb => cb.checked = checked);
    updateBlocBtn();
});

function updateBlocBtn() {
    const selected = document.querySelectorAll('.citire-checkbox:checked').length;
    const btn = document.getElementById('btn-confirma-bloc');
    btn.disabled = selected === 0;
    btn.textContent = selected > 0 ? `✓ Confirmă ${selected} citiri` : '✓ Confirmă selecția';

    // Actualizeaza select-all
    const total = document.querySelectorAll('.citire-checkbox').length;
    document.getElementById('select-all').indeterminate = selected > 0 && selected < total;
    document.getElementById('select-all').checked = selected === total && total > 0;
}
</script>
@endpush
