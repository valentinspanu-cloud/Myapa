@extends('cititor.layout')
@section('title', 'Selectie Luna')
@section('header_title', 'Selectează Luna de Citire')

@section('content')
<div class="max-w-md mx-auto mt-6 px-4">

    {{-- Card principal --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-[#1e3a5f] text-white px-6 py-4">
            <h2 class="text-lg font-semibold">Luna de citire</h2>
            <p class="text-blue-200 text-sm mt-1">Alege perioada pentru care vrei să încarci contoarele</p>
        </div>

        <div class="p-4 space-y-3">
            @foreach($optiuni as $opt)
            <a href="{{ route('cititor.index', ['luna' => $opt['luna'], 'an' => $opt['an']]) }}"
               class="flex items-center justify-between w-full px-4 py-3 rounded-lg border-2 transition-all
                      {{ $opt['luna'] == $lunaAuto && $opt['an'] == $anAuto
                            ? 'border-blue-600 bg-blue-50'
                            : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <div class="text-2xl">
                        {{ $opt['luna'] == $lunaAuto && $opt['an'] == $anAuto ? '📅' : '🗓️' }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 capitalize">{{ $opt['label'] }}</div>
                        <div class="text-xs text-gray-500">
                            Luna {{ $opt['luna'] }}/{{ $opt['an'] }}
                            @if($opt['luna'] == $lunaAuto && $opt['an'] == $anAuto)
                                <span class="text-blue-600 font-medium">— luna curentă</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($opt['cached'])
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                            ⚡ Cache
                        </span>
                    @else
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                            🔄 Oracle
                        </span>
                    @endif
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Legenda --}}
    <div class="mt-4 flex gap-4 text-xs text-gray-500 px-1">
        <span>⚡ <strong>Cache</strong> — se încarcă instant</span>
        <span>🔄 <strong>Oracle</strong> — necesită câteva secunde</span>
    </div>

</div>
@endsection
