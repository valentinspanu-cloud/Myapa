@extends('cititor.layout')
@section('title', 'Lista Contoare')
@section('header_title', 'Ruta: ' . $ruta)

@section('search')
{{-- Statistici sticky --}}
<div class="sticky top-[56px] z-40 bg-[#1e3a5f] text-white px-4 py-2 shadow-md">
    <div class="flex items-center justify-between">
        @php
            $totalContoare = count($contoare);
            $totalAltundeva = count(array_filter($contoare, fn($c) => $c['index_deja_introdus'] ?? false));
            $totalNecitite = $totalContoare - count($citate) - $totalAltundeva;
            $pct = $totalContoare > 0 ? round((count($citate) + $totalAltundeva) / $totalContoare * 100) : 0;
        @endphp
        <div class="grid grid-cols-5 gap-2 flex-1 text-center">
            <div>
                <div class="text-lg font-bold">{{ $totalContoare }}</div>
                <div class="text-xs text-blue-200">Total</div>
            </div>
            <div>
                <div class="text-lg font-bold text-red-300">{{ $totalNecitite }}</div>
                <div class="text-xs text-blue-200">Necitite</div>
            </div>
            <div>
                <div class="text-lg font-bold text-green-300">{{ count($citate) }}</div>
                <div class="text-xs text-blue-200">Citite</div>
            </div>
            <div>
                <div class="text-lg font-bold text-purple-300">{{ $totalAltundeva }}</div>
                <div class="text-xs text-blue-200">Altă sursă</div>
            </div>
            <div>
                <div class="text-lg font-bold text-yellow-300">{{ $pct }}%</div>
                <div class="text-xs text-blue-200">Progres</div>
            </div>
        </div>
    </div>

    {{-- Filtru + Search --}}
    <form method="GET" action="{{ route('cititor.index') }}" class="mt-2 flex gap-2">
        <div class="relative flex-1">
            <input type="text" name="q" id="search-input" value="{{ request('q') }}"
                placeholder="Caută cod, nume, stradă..."
                class="w-full rounded-lg px-3 py-2 pl-8 pr-8 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300"
                autocomplete="off">
            <svg class="w-4 h-4 text-gray-400 absolute left-2 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            @if(request('q'))
            <button type="button" onclick="document.getElementById('search-input').value=''; this.closest('form').submit();"
                class="absolute right-2 top-2 text-gray-400 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            @endif
        </div>
        <select name="filtru" onchange="this.form.submit()"
            class="rounded-lg px-2 py-2 text-sm text-gray-800 focus:outline-none">
            <option value="toate"    {{ request('filtru','toate') == 'toate'    ? 'selected' : '' }}>Toate</option>
            <option value="necitite" {{ request('filtru') == 'necitite' ? 'selected' : '' }}>Necitite</option>
            <option value="citite"   {{ request('filtru') == 'citite'   ? 'selected' : '' }}>Citite</option>
            <option value="altundeva" {{ request('filtru') == 'altundeva' ? 'selected' : '' }}>Are deja citire!</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-2 text-sm">
            ↵
        </button>
    </form>
</div>
@endsection

@section('content')

@php
    $q       = strtolower(trim(request('q', '')));
    $filtru  = request('filtru', 'toate');

    $contoareFiltrate = array_filter($contoare, function($c) use ($q, $filtru, $citate) {
        // Filtru citit/necitit
        $citit = in_array($c['id_cit'], $citate);
        $dejaAltundeva = $c['index_deja_introdus'] ?? false;
        if ($filtru === 'citite'    && !$citit) return false;
        if ($filtru === 'necitite'  && ($citit || $dejaAltundeva)) return false;
        if ($filtru === 'altundeva' && !$dejaAltundeva) return false;

        // Search
        if ($q) {
            $text = strtolower(
                ($c['cod_abonat'] ?? '') . ' ' .
                ($c['nume_abonat'] ?? '') . ' ' .
                ($c['strada'] ?? '') . ' ' .
                ($c['nr_strada'] ?? '')
            );
            return str_contains($text, $q);
        }
        return true;
    });
@endphp

@if(count($contoareFiltrate) === 0)
<div class="text-center py-12 text-gray-500 text-sm bg-white rounded-xl shadow-sm mt-2">
    Niciun rezultat.
</div>
@endif

<div class="space-y-2 mt-2" id="lista_contoare">
@foreach($contoareFiltrate as $contor)
@php
    $citit     = in_array($contor['id_cit'], $citate);
    $cardId    = 'card_' . $contor['id_cit'];
@endphp

<div class="bg-white rounded-xl shadow-sm overflow-hidden" id="{{ $cardId }}">

    {{-- Header card compact --}}
    <button type="button"
        class="w-full px-4 py-3 flex items-center justify-between text-left accordion-btn"
        data-id-cit="{{ $contor['id_cit'] }}"
        data-id-client="{{ $contor['id_client'] }}"
        data-index-vechi="{{ $contor['index_vechi'] }}"
        data-index-oracle="{{ $contor['index_nou'] }}"
        data-cod-abonat="{{ $contor['cod_abonat'] }}"
        data-serie-contor="{{ $contor['serie_contor'] }}"
        data-cod-contor="{{ $contor['cod_contor'] }}"
        data-tip-contor="{{ $contor['tip_contor'] }}"
        data-id-locatie="{{ $contor['id_locatie'] }}"
        data-ruta="{{ $contor['ruta'] }}"
        data-luna="{{ $luna }}"
        data-an="{{ $an }}"
        data-citit="{{ $citit ? '1' : '0' }}"
        data-deja-introdus="{{ ($contor['index_deja_introdus'] ?? false) ? '1' : '0' }}">
        <div class="flex-1 min-w-0">
            <div class="font-semibold text-[#1e3a5f] text-sm truncate">{{ $contor['nume_abonat'] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $contor["strada"] }}, Nr. {{ $contor["nr_strada"] }} · <span class="font-mono font-bold text-[#1e3a5f] text-sm">{{ $contor["cod_abonat"] }}</span></div>
        </div>
        <div class="flex items-center gap-2 ml-3 flex-shrink-0">
            @if($contor['index_deja_introdus'] ?? false)
                <span class="text-xs font-semibold text-purple-600">● ARE DEJA CITIRE!</span>
            @elseif($citit)
                <span class="text-xs font-semibold text-green-600">● CITIT</span>
            @else
                <span class="text-xs font-semibold text-red-500">● NECITIT</span>
            @endif
            <svg class="w-4 h-4 text-gray-400 chevron transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- Continut accordion (ascuns initial) --}}
    <div class="accordion-content hidden border-t border-gray-100">

        {{-- Loading --}}
        <div class="loading-state px-4 py-4 text-center text-sm text-gray-400">
            <svg class="w-5 h-5 animate-spin mx-auto mb-1 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Se încarcă...
        </div>

        {{-- Date (populate via AJAX) --}}
        <div class="data-state hidden">

            {{-- Grid date --}}
            <div class="px-4 py-3 grid grid-cols-3 gap-2 text-sm bg-gray-50">
                <div class="text-center">
                    <div class="text-xs text-gray-400">Sold</div>
                    <div class="font-bold sold-val text-base">—</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-400">Index vechi</div>
                    <div class="font-semibold text-gray-700 index-vechi-val text-base">{{ $contor['index_vechi'] ?? '—' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-400">Index Oracle</div>
                    <div class="font-semibold index-oracle-val text-base {{ $contor['index_nou'] ? 'text-blue-600' : 'text-gray-400' }}">{{ $contor['index_nou'] ?? '—' }}</div>
                </div>
            </div>

            {{-- Cod abonat + serie --}}
            <div class="px-4 py-2 grid grid-cols-2 gap-2 text-xs text-gray-500 border-b border-gray-100">
                <div>Cod: <span class="font-mono font-medium text-gray-700">{{ $contor['cod_abonat'] }}</span></div>
                <div>Serie: <span class="font-mono font-medium text-gray-700">{{ $contor['serie_contor'] }}</span></div>
            </div>

            {{-- Formular citire noua --}}
            <div class="form-necitit px-4 py-3 space-y-3">
                <form class="space-y-3 citire-form" method="POST" action="{{ route('cititor.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_cit"           value="{{ $contor['id_cit'] }}">
                    <input type="hidden" name="cod_abonat"       value="{{ $contor['cod_abonat'] }}">
                    <input type="hidden" name="id_client"        value="{{ $contor['id_client'] }}">
                    <input type="hidden" name="id_locatie"       value="{{ $contor['id_locatie'] }}">
                    <input type="hidden" name="id_contor"        value="{{ $contor['id_contor'] }}">
                    <input type="hidden" name="sector"           value="{{ $contor['sector'] }}">
                    <input type="hidden" name="ruta"             value="{{ $contor['ruta'] }}">
                    <input type="hidden" name="luna"             value="{{ $luna }}">
                    <input type="hidden" name="an"               value="{{ $an }}">
                    <input type="hidden" name="serie_contor"     value="{{ $contor['serie_contor'] }}">
                    <input type="hidden" name="cod_contor"       value="{{ $contor['cod_contor'] }}">
                    <input type="hidden" name="tip_contor"       value="{{ $contor['tip_contor'] }}">
                    <input type="hidden" name="index_vechi"      value="{{ $contor['index_vechi'] }}">
                    <input type="hidden" name="index_nou_oracle" value="{{ $contor['index_nou'] }}">
                    <input type="hidden" name="gps_lat"          class="gps-lat">
                    <input type="hidden" name="gps_lng"          class="gps-lng">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Index citit *</label>
                        <input type="number" name="index_citit" required min="{{ $contor['index_vechi'] ?? 0 }}"
                            placeholder="Introduceți indexul"
                            class="w-full border border-gray-300 rounded-lg px-3 py-3 text-xl font-mono font-bold text-center focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent index-input"
                            data-index-vechi="{{ $contor['index_vechi'] ?? 0 }}"
                >
                        {{-- Consum live --}}
                        <div class="consum-info hidden mt-2">
                            <div class="consum-normal hidden bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-3 py-2 text-sm">
                                <strong>Consum:</strong> <span class="consum-val"></span> m³
                            </div>
                            <div class="consum-avertizare hidden bg-red-50 border border-red-300 text-red-800 rounded-lg px-3 py-2 text-sm">
                                ⚠️ Consum <span class="consum-val-mare"></span> m³ &gt; 40 m³! Verificați.
                            </div>
                            <div class="consum-negativ hidden bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-3 py-2 text-sm">
                                ⚠️ Index mai mic decât cel anterior (<span class="index-vechi-val"></span>).
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 rounded-lg px-3 py-3 hover:bg-gray-50">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm text-gray-500 foto-label">Selectați o poză din galerie</span>
                            <input type="file" name="foto" accept="image/*" class="hidden"
                                onchange="this.previousElementSibling.textContent='✓ Poză capturată'; this.previousElementSibling.classList.add('text-green-600');">
                        </label>
                    </div>

                    <input type="text" name="observatii" maxlength="500"
                        placeholder="Observații (opțional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1e3a5f]">

                    <div class="text-xs text-gray-400 gps-status">📍 Locație: în așteptare...</div>

                    <button type="submit"
                        class="w-full bg-[#1e3a5f] hover:bg-[#2d5a8e] text-white font-semibold py-3 rounded-xl transition-colors text-base submit-btn">
                        Salvează citirea
                    </button>
                </form>
            </div>

            {{-- Citit — arata index + buton editare --}}
            <div class="form-citit hidden px-4 py-3">
                <div class="bg-green-50 rounded-lg p-3 mb-3 grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-xs text-gray-500">Index citit</div>
                        <div class="text-2xl font-bold text-[#1e3a5f] index-citit-val">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Consum</div>
                        <div class="text-2xl font-bold text-blue-600 consum-citit-val">—</div>
                    </div>
                </div>
                <div class="respins-info hidden bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                    <div class="text-sm font-semibold text-orange-700 mb-1">↩ Citire respinsă de supervisor</div>
                    <div class="text-xs text-orange-600 respins-motiv"></div>
                </div>
                <div class="foto-container hidden mb-3">
                    <img class="foto-img w-full rounded-lg object-cover max-h-48" src="" alt="Poză contor">
                </div>
                <a href="#" class="edit-link block w-full text-center border border-[#1e3a5f] text-[#1e3a5f] font-semibold py-2 rounded-xl text-sm hover:bg-gray-50">
                    Editează citirea
                </a>
            </div>

        </div>{{-- end data-state --}}
    </div>{{-- end accordion-content --}}
</div>{{-- end card --}}

@endforeach
</div>

@endsection

@push('scripts')
<script>
const DETALIU_URL = "{{ url('cititor/detaliu') }}";
const STORE_URL   = "{{ route('cititor.store') }}";
const EDIT_URL    = "{{ url('cititor/editeaza') }}";
const CSRF        = document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}';

let activeCard = null;
const PRAG_CONSUM = 40;

function calcConsum(input) {
    const indexVechi = parseInt(input.dataset.indexVechi) || 0;
    const indexNou   = parseInt(input.value) || 0;
    const consum     = indexNou - indexVechi;
    const info = input.parentElement.querySelector('.consum-info');
    if (!info) return;
    const normal  = info.querySelector('.consum-normal');
    const avert   = info.querySelector('.consum-avertizare');
    const negativ = info.querySelector('.consum-negativ');

    normal.style.display = "none";
    avert.style.display = "none";
    negativ.style.display = "none";

    if (!input.value) { info.style.display = 'none'; return; }
    info.style.display = 'block';

    if (consum < 0) {
        negativ.style.display = 'block';
        negativ.querySelector('.index-vechi-val').textContent = indexVechi;
    } else if (consum > PRAG_CONSUM) {
        avert.style.display = 'block';
        avert.querySelector('.consum-val-mare').textContent = consum;
    } else {
        normal.style.display = 'block';
        normal.querySelector('.consum-val').textContent = consum;
    }
}

// Event delegation pentru calcul consum
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('index-input')) {
        calcConsum(e.target);
    }
});

let gpsLat = null, gpsLng = null;

// GPS
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
        gpsLat = pos.coords.latitude.toFixed(7);
        gpsLng = pos.coords.longitude.toFixed(7);
        document.querySelectorAll('.gps-status').forEach(el => {
            el.textContent = `📍 Locație capturată`;
            el.classList.add('text-green-600');
        });
        document.querySelectorAll('.gps-lat').forEach(el => el.value = gpsLat);
        document.querySelectorAll('.gps-lng').forEach(el => el.value = gpsLng);
    }, () => {
        document.querySelectorAll('.gps-status').forEach(el => el.textContent = '📍 Locație: nedetectată (opțional)');
    }, { enableHighAccuracy: true, timeout: 10000 });
}

// Accordion
document.querySelectorAll('.accordion-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card    = this.closest('[id^="card_"]');
        const content = card.querySelector('.accordion-content');
        const chevron = card.querySelector('.chevron');
        const idCit   = this.dataset.idCit;
        const idClient= this.dataset.idClient;

        // Inchidem cardul activ daca e altul
        if (activeCard && activeCard !== card) {
            activeCard.querySelector('.accordion-content').classList.add('hidden');
            activeCard.querySelector('.chevron').classList.remove('rotate-180');
        }

        // Toggle card curent
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            activeCard = card;

            // Verificam daca e deja introdus altundeva
            if (this.dataset.dejaIntrodus === '1' && !card.dataset.loaded) {
                incarcaDetaliu(card, idClient, idCit, true);
            } else if (!card.dataset.loaded) {
                incarcaDetaliu(card, idClient, idCit);
            }
        } else {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
            activeCard = null;
        }
    });
});

function incarcaDetaliu(card, idClient, idCit, dejaAltundeva = false) {
    const btn    = card.querySelector('.accordion-btn');
    const citit  = btn.dataset.citit === '1';

    fetch(`${DETALIU_URL}/${idClient}/${idCit}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Sold
        const sold = data.sold;
        const soldEl = card.querySelector('.sold-val');
        if (sold !== null) {
            soldEl.textContent = parseFloat(sold).toFixed(2) + ' RON';
            soldEl.className = 'font-bold sold-val text-base ' + (sold > 0 ? 'text-red-600' : 'text-green-600');
        } else {
            soldEl.textContent = '—';
        }

        // Ascundem loading, afisam date
        card.querySelector('.loading-state').classList.add('hidden');
        card.querySelector('.data-state').classList.remove('hidden');

        // Daca e deja introdus altundeva — blocam indexul dar permitem poza si GPS
        if (dejaAltundeva) {
            const inputIndex = card.querySelector('input[name="index_citit"]');
            if (inputIndex) {
                inputIndex.disabled = true;
                inputIndex.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            }
            const btnSalveaza = card.querySelector('.btn-salveaza');
            if (btnSalveaza) {
                btnSalveaza.disabled = true;
                btnSalveaza.classList.add('opacity-50', 'cursor-not-allowed');
            }
            // Afisam mesaj info
            const msg = document.createElement('div');
            msg.className = 'px-4 py-2 text-xs text-purple-700 bg-purple-50 border-t border-purple-100';
            msg.textContent = '⚠️ Index deja înregistrat prin altă metodă — câmpul e blocat. Poți adăuga poză și GPS.';
            card.querySelector('.data-state').prepend(msg);
        }

        if (data.status && data.status !== null) {
            // Citit — afisam index citit + buton editare
            card.querySelector('.form-necitit').classList.add('hidden');
            card.querySelector('.form-citit').classList.remove('hidden');
            card.querySelector('.index-citit-val').textContent = data.index_citit ?? '—';
            const indexVechi = parseInt(card.querySelector('.accordion-btn').dataset.indexVechi) || 0;
            const consum = data.index_citit ? data.index_citit - indexVechi : null;
            card.querySelector('.consum-citit-val').textContent = consum !== null ? consum + ' m³' : '—';

            // Afisam mesaj respingere/eroare daca e cazul
            if (data.status === 'respins') {
                card.querySelector('.respins-info').style.display = 'block';
                card.querySelector('.respins-info').className = card.querySelector('.respins-info').className.replace('orange', 'orange');
                card.querySelector('.respins-motiv').textContent = '↩ Respins: ' + (data.observatii ?? '');
            } else if (data.status === 'eroare') {
                card.querySelector('.respins-info').style.display = 'block';
                card.querySelector('.respins-motiv').textContent = '⚠️ Eroare: ' + (data.mesaj_oracle ?? data.observatii ?? '');
            } else {
                card.querySelector('.respins-info').style.display = 'none';
            }
            if (data.citire_id) {
                card.querySelector('.edit-link').href = `${EDIT_URL}/${data.citire_id}`;
            }
        } else {
            // Necitit — afisam formular
            card.querySelector('.form-necitit').classList.remove('hidden');
            card.querySelector('.form-citit').classList.add('hidden');
            // GPS
            if (gpsLat) {
                card.querySelector('.gps-lat').value = gpsLat;
                card.querySelector('.gps-lng').value = gpsLng;
                card.querySelector('.gps-status').textContent = '📍 Locație capturată';
                card.querySelector('.gps-status').classList.add('text-green-600');
            }
        }

        card.dataset.loaded = '1';
    })
    .catch(() => {
        card.querySelector('.loading-state').textContent = 'Eroare la încărcare.';
    });
}

// Submit formular citire
document.addEventListener('submit', function(e) {
    if (!e.target.classList.contains('citire-form')) return;
    const form = e.target;
    const fotoInput = form.querySelector('input[type="file"]');
    const hasFoto = fotoInput && fotoInput.files && fotoInput.files.length > 0;
    const btn = form.querySelector('.submit-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Se salvează...'; }
    if (hasFoto) {
        return; // submit clasic cu poza
    }
    e.preventDefault();
    const formData = new FormData(form);
    formData.append('_token', CSRF);
    fetch(STORE_URL, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = form.closest('[id^="card_"]');
            const badgeEl = card.querySelector('.accordion-btn span');
            badgeEl.textContent = '● CITIT';
            badgeEl.className = 'text-xs font-semibold text-green-600';
            card.dataset.loaded = '';
            const btn2 = card.querySelector('.accordion-btn');
            incarcaDetaliu(card, btn2.dataset.idClient, btn2.dataset.idCit);
        } else {
            if (btn) { btn.disabled = false; btn.textContent = 'Salvează citirea'; }
            alert(data.message || 'Eroare la salvare.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Salvează citirea'; }
        alert('Eroare de retea.');
    });
});
</script>
@endpush
