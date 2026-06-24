@extends('cititor.layout')
@section('title', 'Cititor nou')
@section('header_title', 'Cititor nou')

@section('content')

<form method="POST" action="{{ route('cititor.supervisor.cititori.store') }}" class="space-y-4">
    @csrf

    <div class="bg-white rounded-xl shadow-sm p-4 space-y-4">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Nume complet *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1e3a5f]">
            @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                placeholder="prenumenume@aquaservtulcea.ro"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1e3a5f]">
            @error('email')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Rută *</label>
            <select name="ruta" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1e3a5f]">
                <option value="">Selectați ruta...</option>
                @foreach($rute as $ruta)
                <option value="{{ $ruta->nume }}" {{ old('ruta') == $ruta->nume ? 'selected' : '' }}>
                    {{ $ruta->nume }}
                </option>
                @endforeach
            </select>
            @error('ruta')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-xs text-blue-700">
            Parola temporară: <strong>Schimba@{{ now()->year }}!</strong> — de comunicat cititorului.
        </div>

    </div>

    <button type="submit"
        class="w-full bg-[#1e3a5f] hover:bg-[#2d5a8e] text-white font-semibold py-3 rounded-xl transition-colors">
        Creează cititor
    </button>

    <a href="{{ route('cititor.supervisor.cititori.index') }}"
        class="block text-center text-[#1e3a5f] text-sm font-medium py-2">← Anulează</a>
</form>

@endsection
