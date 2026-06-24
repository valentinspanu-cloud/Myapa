@extends('cititor.layout')
@section('title', 'Editare citire ' . $citire->cod_abonat)
@section('header_title', 'Editare citire')

@section('content')

<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
    <div class="px-4 py-3 bg-[#1e3a5f] text-white">
        <div class="font-semibold">{{ $citire->cod_abonat }}</div>
        <div class="text-xs text-blue-200">{{ $citire->ruta }} · {{ $citire->luna }}/{{ $citire->an }}</div>
    </div>
    <div class="px-4 py-3 grid grid-cols-2 gap-2 text-sm border-b border-gray-100">
        <div>
            <div class="text-xs text-gray-400">Index vechi</div>
            <div class="font-semibold">{{ $citire->index_vechi ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400">Index salvat</div>
            <div class="font-semibold text-[#1e3a5f]">{{ $citire->index_citit }}</div>
        </div>
    </div>
</div>

<form action="{{ route('cititor.update', $citire) }}" method="POST"
    enctype="multipart/form-data" class="space-y-4">
    @csrf

    <input type="hidden" name="gps_lat" id="gps_lat">
    <input type="hidden" name="gps_lng" id="gps_lng">

    {{-- Index --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <label class="block text-xs font-medium text-gray-600 mb-1">Index citit *</label>
        <input type="number" name="index_citit" required
            value="{{ $citire->index_citit }}"
            min="{{ $citire->index_vechi ?? 0 }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-3 text-xl font-mono font-bold text-center focus:ring-2 focus:ring-[#1e3a5f] index-input"
            data-index-vechi="{{ $citire->index_vechi ?? 0 }}"
            oninput="calcConsum(this)">
    </div>
    <div id="consum-info-edit" style="display:none;" class="mt-2">
        <div id="consum-normal-edit" style="display:none;" class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-3 py-2 text-sm"><strong>Consum:</strong> <span id="consum-val-edit"></span> m³</div>
        <div id="consum-avert-edit" style="display:none;" class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-3 py-2 text-sm">⚠️ Consum <span id="consum-mare-edit"></span> m³ > 40 m³! Verificați.</div>
        <div id="consum-neg-edit" style="display:none;" class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-3 py-2 text-sm">⚠️ Index mai mic decât cel anterior (<span id="index-vechi-edit"></span>).</div>
    </div>

    {{-- Foto --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Fotografie contor</label>

        {{-- Poza existenta --}}
        @if($citire->foto_path)
        <div class="mb-3">
            <div class="text-xs text-gray-400 mb-1">Poza actuală</div>
            <img src="{{ route("cititor.foto", ltrim(str_replace("citiri/", "", $citire->foto_path), "/")) }}" alt="Poza actuala"
                class="w-full rounded-lg object-cover max-h-48 border border-gray-200">
        </div>
        @endif

        <label class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 rounded-lg px-3 py-3 hover:bg-gray-50">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-sm text-gray-500" id="foto_label">
                {{ $citire->foto_path ? 'Înlocuiți poza' : 'Faceți o poză' }}
            </span>
            <input type="file" name="foto" accept="image/*" class="hidden"
                onchange="updatePreview(this)">
        </label>

        {{-- Preview poza noua --}}
        <div id="foto_preview" class="hidden mt-2">
            <img id="foto_img" src="" alt="Preview"
                class="w-full rounded-lg object-cover max-h-48 border-2 border-green-400">
            <div class="text-xs text-green-600 mt-1 text-center font-medium">✓ Fotografie nouă capturată</div>
        </div>
    </div>

    {{-- Observatii --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <label class="block text-xs font-medium text-gray-600 mb-1">Observații</label>
        <input type="text" name="observatii" maxlength="500"
            value="{{ $citire->observatii }}"
            placeholder="ex: contor defect, inaccesibil..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1e3a5f]">
    </div>

    {{-- GPS --}}
    <div class="flex items-center gap-2 text-xs text-gray-500 px-1" id="gps_status">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        </svg>
        <span>Locație: se determină...</span>
    </div>

    <button type="submit"
        class="w-full bg-[#1e3a5f] hover:bg-[#2d5a8e] text-white font-semibold py-3 rounded-xl transition-colors text-base">
        Salvează modificările
    </button>
</form>

<a href="{{ route('cititor.index') }}"
    class="mt-3 flex items-center gap-2 text-[#1e3a5f] text-sm font-medium py-2">
    ← Înapoi la lista
</a>

@endsection

@push('scripts')
<script>
const PRAG_CONSUM = 40;
function calcConsum(input) {
    const iv = parseInt(input.dataset.indexVechi)||0;
    const in_ = parseInt(input.value)||0;
    const c = in_ - iv;
    const info = document.getElementById("consum-info-edit");
    const norm = document.getElementById("consum-normal-edit");
    const avert = document.getElementById("consum-avert-edit");
    const neg = document.getElementById("consum-neg-edit");
    norm.style.display="none"; avert.style.display="none"; neg.style.display="none";
    if (!input.value) { info.style.display="none"; return; }
    info.style.display="block";
    if (c < 0) { neg.style.display="block"; document.getElementById("index-vechi-edit").textContent=iv; }
    else if (c > PRAG_CONSUM) { avert.style.display="block"; document.getElementById("consum-mare-edit").textContent=c; }
    else { norm.style.display="block"; document.getElementById("consum-val-edit").textContent=c; }
}
function updatePreview(input) {
    const label   = document.getElementById('foto_label');
    const preview = document.getElementById('foto_preview');
    const img     = document.getElementById('foto_img');
    if (input.files && input.files[0]) {
        label.textContent = '✓ Poză capturată';
        label.classList.add('text-green-600');
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Prevenim submit dublu
document.querySelector('form').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Se salvează...';
        btn.classList.add('opacity-60');
    }
});

// GPS
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('gps_lat').value = pos.coords.latitude.toFixed(7);
        document.getElementById('gps_lng').value = pos.coords.longitude.toFixed(7);
        document.querySelector('#gps_status span').textContent =
            `Locație capturată (${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)})`;
        document.getElementById('gps_status').classList.add('text-green-600');
    }, () => {
        document.querySelector('#gps_status span').textContent = 'Locație indisponibilă';
    }, { enableHighAccuracy: true, timeout: 10000 });
}
</script>
@endpush
