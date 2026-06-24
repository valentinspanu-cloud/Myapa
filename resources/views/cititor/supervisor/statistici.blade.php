@extends('cititor.layout')
@section('title', 'Statistici Citiri')
@section('header_title', 'Statistici Citiri')

@section('content')

{{-- Filtre --}}
<form method="GET" class="bg-white rounded-xl shadow-sm p-3 mb-4 flex gap-2">
    <select name="luna" class="border border-gray-300 rounded-lg px-2 py-2 text-sm flex-1">
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
    <button type="submit" class="bg-[#1e3a5f] text-white rounded-lg px-4 py-2 text-sm font-medium">
        Filtrează
    </button>
</form>

{{-- Tabel statistici --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-4 py-3 bg-[#1e3a5f] text-white text-sm font-semibold">
        Statistici per rută — {{ \Carbon\Carbon::create($an, $luna)->translatedFormat('F Y') }}
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Rută</th>
                    <th class="px-4 py-3 text-center">Total</th>
                    <th class="px-4 py-3 text-center">Necitite</th>
                    <th class="px-4 py-3 text-center text-blue-600">Noi</th>
                    <th class="px-4 py-3 text-center text-green-600">Confirmate</th>
                    <th class="px-4 py-3 text-center text-red-600">Erori</th>
                    <th class="px-4 py-3 text-center text-orange-600">Respinse</th>
                    <th class="px-4 py-3 text-center text-gray-500">Expirate</th>
                    <th class="px-4 py-3 text-center text-purple-600">Altă sursă</th>
                    <th class="px-4 py-3 text-center">Progres</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($statistici as $s)
            @php $citate = $s['nou'] + $s['confirmat'] + $s['eroare'] + $s['respins'] + $s['expirat'] + $s['altundeva']; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="font-semibold text-[#1e3a5f]">{{ $s['ruta'] }}</div>
                    <div class="text-xs text-gray-400">{{ $s['cititor'] }}</div>
                </td>
                <td class="px-4 py-3 text-center">{{ $s['total'] ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $s['necitit'] }}</td>
                <td class="px-4 py-3 text-center text-blue-600 font-medium">{{ $s['nou'] }}</td>
                <td class="px-4 py-3 text-center text-green-600 font-medium">{{ $s['confirmat'] }}</td>
                <td class="px-4 py-3 text-center text-red-600 font-medium">{{ $s['eroare'] ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-orange-600 font-medium">{{ $s['respins'] ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $s['expirat'] ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-purple-600 font-medium">{{ $s['altundeva'] ?: '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($s['total'] > 0)
                    @php $pct = round($citate / $s['total'] * 100); @endphp
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-[#1e3a5f] h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-[#1e3a5f] w-8">{{ $pct }}%</span>
                    </div>
                    @else
                    <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            {{-- Total general --}}
            @php
                $totTotal     = array_sum(array_column($statistici, 'total'));
                $totNou       = array_sum(array_column($statistici, 'nou'));
                $totConfirmat = array_sum(array_column($statistici, 'confirmat'));
                $totEroare    = array_sum(array_column($statistici, 'eroare'));
                $totRespins   = array_sum(array_column($statistici, 'respins'));
                $totExpirat   = array_sum(array_column($statistici, 'expirat'));
                $totNecitit   = array_sum(array_column($statistici, 'necitit'));
                $totAltundeva = array_sum(array_column($statistici, 'altundeva'));
                $totCitate    = $totNou + $totConfirmat + $totEroare + $totRespins + $totExpirat + $totAltundeva;
                $totPct       = $totTotal > 0 ? round($totCitate / $totTotal * 100) : 0;
            @endphp
            <tfoot class="bg-gray-50 font-bold text-sm border-t-2 border-gray-200">
                <tr>
                    <td class="px-4 py-3">TOTAL</td>
                    <td class="px-4 py-3 text-center">{{ $totTotal }}</td>
                    <td class="px-4 py-3 text-center">{{ $totNecitit }}</td>
                    <td class="px-4 py-3 text-center text-blue-600">{{ $totNou }}</td>
                    <td class="px-4 py-3 text-center text-green-600">{{ $totConfirmat }}</td>
                    <td class="px-4 py-3 text-center text-red-600">{{ $totEroare ?: '—' }}</td>
                    <td class="px-4 py-3 text-center text-orange-600">{{ $totRespins ?: '—' }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $totExpirat ?: '—' }}</td>
                    <td class="px-4 py-3 text-center text-purple-600">{{ $totAltundeva ?: '—' }}</td>
                    <td class="px-4 py-3 text-center font-bold text-[#1e3a5f]">{{ $totPct }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('cititor.supervisor.index') }}"
        class="text-[#1e3a5f] text-sm font-medium">← Înapoi la citiri</a>
</div>

@endsection
