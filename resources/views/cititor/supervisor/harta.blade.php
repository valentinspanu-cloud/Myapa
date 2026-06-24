@extends('cititor.layout')
@section('title', 'Hartă Citiri')
@section('header_title', 'Hartă Citiri')

@section('content')

{{-- Harta --}}
<div id="map" class="rounded-xl shadow-sm mb-4" style="height: 80vh;"></div>

{{-- Legenda --}}
<div class="bg-white rounded-xl shadow-sm p-3 mb-4 flex gap-4 text-xs flex-wrap">
    <span>🟢 Confirmat</span>
    <span>🔵 Nou</span>
    <span>🔴 Eroare</span>
    <span>🟠 Respins</span>
    <span class="ml-auto font-semibold text-[#1e3a5f]">{{ count($citiri) }} puncte GPS</span>
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
    <div class="col-span-2 border border-gray-300 rounded-lg p-2">
        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-gray-200">
            <input type="checkbox" id="toate-rutele" class="rounded accent-[#1e3a5f]"
                {{ empty($ruteSelectate) ? 'checked' : '' }}>
            <label for="toate-rutele" class="text-sm font-semibold text-[#1e3a5f] cursor-pointer">Toate rutele</label>
        </div>
        <div class="flex flex-wrap gap-3">
            @foreach($rute as $r)
            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                <input type="checkbox" name="ruta[]" value="{{ $r }}" class="rounded accent-[#1e3a5f] ruta-checkbox"
                    {{ in_array($r, $ruteSelectate ?? []) ? 'checked' : '' }}>
                {{ $r }}
            </label>
            @endforeach
        </div>
    </div>
    <button type="submit" class="bg-[#1e3a5f] text-white rounded-lg py-2 text-sm font-medium">
        Filtrează
    </button>
</form>

<div class="mt-4">
    <a href="{{ route('cititor.supervisor.index') }}"
        class="text-[#1e3a5f] text-sm font-medium">← Înapoi la citiri</a>
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.getElementById('toate-rutele').addEventListener('change', function() {
    if (this.checked) {
        document.querySelectorAll('.ruta-checkbox').forEach(cb => cb.checked = false);
        this.closest('form').submit();
    }
});

document.querySelectorAll('.ruta-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('toate-rutele').checked = false;
        }
        const vreoRutaBifata = [...document.querySelectorAll('.ruta-checkbox')].some(c => c.checked);
        if (!vreoRutaBifata) {
            document.getElementById('toate-rutele').checked = true;
        }
        this.closest('form').submit();
    });
});

const citiri = @json($citiri);
const strazi = @json($strazi);
const cititoriPerRuta = @json($cititoriPerRuta);

const map = L.map('map').setView([45.1791, 28.7955], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

const culori = {
    'confirmat': '#16a34a',
    'nou':       '#2563eb',
    'eroare':    '#dc2626',
    'respins':   '#ea580c',
    'corectat':  '#d97706',
};

// Culori distincte per ruta pentru strazi
const culoriRute = {};
const paletaRute = ['#ff0000', '#00d9ff', '#ff00ff', '#ffaa00', '#00ff00', '#0066ff', '#ff0099', '#aaff00', '#9900ff'];
let idxCuloare = 0;

function culoareRuta(ruta) {
    if (!culoriRute[ruta]) {
        culoriRute[ruta] = paletaRute[idxCuloare % paletaRute.length];
        idxCuloare++;
    }
    return culoriRute[ruta];
}

// Layer 1: Strazile rutei - cate un layer separat per ruta
const layereRute = {};

strazi.forEach(function(s) {
    if (!s.geojson || !s.geojson.coordinates) return;

    if (!layereRute[s.ruta]) {
        layereRute[s.ruta] = L.layerGroup();
    }

    const culoare = culoareRuta(s.ruta);

    // Tratam atat LineString cat si Polygon/MultiLineString
    let coordsRaw = s.geojson.coordinates;
    let coordsArrays = [];

    if (s.geojson.type === 'LineString') {
        coordsArrays = [coordsRaw];
    } else if (s.geojson.type === 'Polygon') {
        coordsArrays = coordsRaw; // array de ring-uri
    } else if (s.geojson.type === 'MultiLineString') {
        coordsArrays = coordsRaw;
    } else {
        return; // tip necunoscut, skip
    }

    coordsArrays.forEach(function(ring) {
        const coords = ring.map(c => [c[1], c[0]]); // lat,lng
        const linie = L.polyline(coords, {
            color: culoare,
            weight: 6,
            opacity: 0.9
        });
        linie.bindPopup(`<strong>${s.strada}</strong><br><span style="font-size:11px;color:#666">${s.ruta}</span>`);
        layereRute[s.ruta].addLayer(linie);
    });
    return;

    linie.bindPopup(`<strong>${s.strada}</strong><br><span style="font-size:11px;color:#666">${s.ruta}</span>`);
    layereRute[s.ruta].addLayer(linie);
});

// Adaugam toate layerele pe harta implicit
Object.values(layereRute).forEach(layer => layer.addTo(map));

// Pentru bounds, combinam toate layerele intr-un singur featureGroup virtual
const strazileLayer = L.layerGroup(Object.values(layereRute).flatMap(lg => lg.getLayers()));

const markers = [];
const citiriLayer = L.layerGroup();

citiri.forEach(function(c) {
    if (!c.lat || !c.lng) return;

    const culoare = culori[c.status] || '#6b7280';

    const marker = L.circleMarker([c.lat, c.lng], {
        radius: 8,
        fillColor: culoare,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.9
    });
    citiriLayer.addLayer(marker);

    marker.bindPopup(`
        <div style="min-width:180px">
            <div style="font-weight:600;color:#1e3a5f;margin-bottom:4px">${c.nume}</div>
            <div style="font-size:11px;color:#666">${c.strada}</div>
            <div style="font-size:11px;color:#666">Cod: ${c.cod_abonat}</div>
            <hr style="margin:6px 0">
            <div style="font-size:12px">Index citit: <strong>${c.index_citit ?? '—'}</strong></div>
            <div style="font-size:12px">Consum: <strong>${c.consum ?? '—'} m³</strong></div>
            <div style="font-size:12px">Cititor: ${c.cititor}</div>
            <div style="font-size:12px">Data: ${c.data_citire}</div>
            <div style="font-size:12px">Rută: ${c.ruta}</div>
            <div style="margin-top:6px">
                <span style="background:${culoare};color:#fff;padding:2px 8px;border-radius:10px;font-size:11px">${c.status}</span>
            </div>
        </div>
    `);

    markers.push(marker);
});

citiriLayer.addTo(map);

// Calculam bounds manual din toate coordonatele disponibile
let allLats = [];
let allLngs = [];

citiri.forEach(c => {
    if (c.lat && c.lng) { allLats.push(c.lat); allLngs.push(c.lng); }
});

function extrageToatePuncte(geojson) {
    if (!geojson || !geojson.coordinates) return [];
    if (geojson.type === 'LineString') return geojson.coordinates;
    if (geojson.type === 'Polygon' || geojson.type === 'MultiLineString') {
        return geojson.coordinates.flat();
    }
    return [];
}

strazi.forEach(s => {
    extrageToatePuncte(s.geojson).forEach(coord => {
        if (Array.isArray(coord) && coord.length === 2) {
            allLngs.push(coord[0]);
            allLats.push(coord[1]);
        }
    });
});

if (allLats.length > 0) {
    const bounds = L.latLngBounds(
        [Math.min(...allLats), Math.min(...allLngs)],
        [Math.max(...allLats), Math.max(...allLngs)]
    );
    map.fitBounds(bounds.pad(0.1));
} else {
    map.setView([45.1791, 28.7955], 13);
}

// Layer control: toggle strazi on/off
const overlaysControl = {};

overlaysControl['📍 Citiri GPS'] = citiriLayer;

Object.keys(layereRute).forEach(ruta => {
    const culoare = culoriRute[ruta] || '#666';
    const cititor = cititoriPerRuta[ruta] || '';
    const label = `<span style="display:inline-block;width:12px;height:12px;background:${culoare};border-radius:2px;margin-right:6px;vertical-align:middle"></span><strong>${ruta}</strong>${cititor ? ' - ' + cititor : ''}`;
    overlaysControl[label] = layereRute[ruta];
});

L.control.layers(null, overlaysControl, { collapsed: false }).addTo(map);
</script>
@endpush
