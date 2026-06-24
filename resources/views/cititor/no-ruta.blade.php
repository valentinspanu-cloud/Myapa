@extends('cititor.layout')
@section('title', 'Fără rută')
@section('header_title', 'Cititor Contoare')
@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <h2 class="text-lg font-semibold text-gray-700 mb-2">Nicio rută alocată</h2>
    <p class="text-gray-500 text-sm">Contul tău nu are o rută de citire alocată.<br>Contactează administratorul.</p>
</div>
@endsection
