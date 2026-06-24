@extends('cititor.layout')
@section('title', 'Gestiune Cititori')
@section('header_title', 'Gestiune Cititori')

@section('content')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-xl text-sm mb-4">
    {{ session('success') }}
</div>
@endif

<div class="flex gap-2 mb-4">
    <a href="{{ route('cititor.supervisor.rute.index') }}"
        class="bg-gray-100 text-[#1e3a5f] px-4 py-2 rounded-xl text-sm font-medium">
        🗺 Rute
    </a>
</div>
<div class="flex gap-2 mb-4">
    <a href="{{ route('cititor.supervisor.rute.index') }}"
        class="bg-gray-100 text-[#1e3a5f] px-4 py-2 rounded-xl text-sm font-medium">
        🗺 Rute
    </a>
</div>
<div class="flex items-center justify-between mb-4">
    <div class="text-sm text-gray-500">{{ $cititori->count() }} cititori</div>
    <a href="{{ route('cititor.supervisor.cititori.create') }}"
        class="bg-[#1e3a5f] text-white px-4 py-2 rounded-xl text-sm font-medium">
        + Cititor nou
    </a>
</div>

<div class="space-y-3">
@forelse($cititori as $cititor)
<div class="bg-white rounded-xl shadow-sm overflow-hidden {{ $cititor->status != 1 ? 'opacity-50' : '' }}">
    <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="font-semibold text-[#1e3a5f] text-sm">{{ $cititor->name }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $cititor->email }}</div>
            <div class="mt-1">
                <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                    {{ $cititor->ruta ?? 'Fără rută' }}
                </span>
                @if($cititor->status != 1)
                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full ml-1">Inactiv</span>
                @endif
            </div>
        </div>
        <div class="flex flex-col gap-1 ml-3 flex-shrink-0">
            <a href="{{ route('cititor.supervisor.cititori.edit', $cititor) }}"
                class="text-xs bg-gray-100 text-[#1e3a5f] px-3 py-1.5 rounded-lg font-medium text-center">
                Editează
            </a>
            <form method="POST" action="{{ route('cititor.supervisor.cititori.reset-parola', $cititor) }}">
                @csrf
                <button type="submit" onclick="return confirm('Resetezi parola pentru {{ $cititor->name }}?')"
                    class="w-full text-xs bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg font-medium">
                    Reset parolă
                </button>
            </form>
            <form method="POST" action="{{ route('cititor.supervisor.cititori.toggle-status', $cititor) }}">
                @csrf
                <button type="submit"
                    class="w-full text-xs {{ $cititor->status == 1 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} px-3 py-1.5 rounded-lg font-medium">
                    {{ $cititor->status == 1 ? 'Dezactivează' : 'Activează' }}
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="text-center py-12 text-gray-500 text-sm">
    Niciun cititor înregistrat.
</div>
@endforelse
</div>

<div class="mt-6">
    <a href="{{ route('cititor.supervisor.index') }}"
        class="text-[#1e3a5f] text-sm font-medium">← Înapoi la citiri</a>
</div>

@endsection
