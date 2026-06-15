@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
<div class="row justify-content-center">
<div class="col-lg-11">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div style="background:#1a5276;border-radius:8px;padding:10px 14px;margin-right:14px;">
                <i class="ti ti-mail-forward" style="font-size:22px;color:#fff;"></i>
            </div>
            <div>
                <h4 class="mb-0" style="color:#1a5276;font-weight:700;">Trimitere Facturi</h4>
                <small class="text-muted">{{ count($facturi) }} facturi găsite pentru {{ $lunaText }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.facturi.export', ['luna' => $luna_sel, 'an' => $an_sel]) }}"
               class="btn btn-outline-success btn-sm">
                <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
            </a>
            <a href="{{ route('admin.facturi.log') }}" class="btn btn-outline-primary btn-sm">
                <i class="ti ti-history me-1"></i>Log
            </a>
            <a href="{{ route('admin.extern-clienti.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-users me-1"></i>Clienți externi
            </a>
        </div>
    </div>

    {{-- Mesaje --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('batch_id') || (isset($activeBatch) && $activeBatch))
    @php
        $showBatchId    = session('batch_id') ?? $activeBatch->batch_id;
        $showBatchLuna  = session('batch_luna') ?? $activeBatch->luna;
        $showBatchTotal = session('batch_total') ?? $activeBatch->total;
        $showTrimise    = $activeBatch->trimise ?? 0;
        $showEsuate     = $activeBatch->esuate ?? 0;
        $showProcesat   = $showTrimise + $showEsuate;
        $showProcent    = $showBatchTotal > 0 ? round(($showProcesat / $showBatchTotal) * 100) : 0;
        $showDone       = isset($activeBatch) && $activeBatch && $activeBatch->status === 'completed';
    @endphp
    <div class="card shadow-sm mb-4 border-0" style="border-left:4px solid #27ae60 !important;" id="batchProgress">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <i class="ti ti-send me-2" style="color:#27ae60;"></i>
                    <strong>Trimitere {{ $showDone ? 'finalizată' : 'în progres' }}</strong>
                    — <span id="batchLuna">{{ $showBatchLuna }}</span>
                </div>
                <div class="text-muted small">
                    <span id="batchProcesat">{{ $showProcesat }}</span> / <span id="batchTotal">{{ $showBatchTotal }}</span> procesate
                </div>
            </div>
            <div class="progress mb-2" style="height:20px;">
                <div class="progress-bar progress-bar-striped {{ $showDone ? '' : 'progress-bar-animated' }} bg-success"
                     id="batchBar" role="progressbar" style="width:{{ $showProcent }}%">{{ $showProcent }}%</div>
            </div>
            <div class="d-flex gap-3 small text-muted">
                <span><i class="ti ti-circle-check text-success me-1"></i>Trimise: <strong id="batchTrimise">{{ $showTrimise }}</strong></span>
                <span><i class="ti ti-circle-x text-danger me-1"></i>Erori: <strong id="batchEsuate">{{ $showEsuate }}</strong></span>
                @if($showDone)
                    <span class="text-success fw-bold"><i class="ti ti-check me-1"></i>Finalizat!</span>
                @endif
            </div>
        </div>
    </div>
    @if(!$showDone)
    <script>
    (function() {
        const batchId  = '{{ $showBatchId }}';
        const interval = setInterval(function() {
            fetch('/admin/facturi/batch-status?batch_id=' + batchId)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('batchProcesat').textContent = data.procesat;
                    document.getElementById('batchTotal').textContent    = data.total;
                    document.getElementById('batchTrimise').textContent  = data.trimise;
                    document.getElementById('batchEsuate').textContent   = data.esuate;
                    document.getElementById('batchBar').style.width      = data.procent + '%';
                    document.getElementById('batchBar').textContent      = data.procent + '%';

                    if (data.status === 'completed') {
                        clearInterval(interval);
                        location.reload();
                    }
                });
        }, 5000);
    })();
    </script>
    @endif
    @endif

    {{-- Selector lună --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.facturi.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="form-label fw-semibold mb-1">Luna</label>
                    <select name="luna" class="form-select" style="min-width:120px;">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $luna_sel == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate(2000, $m, 1)->locale('ro')->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label fw-semibold mb-1">Anul</label>
                    <select name="an" class="form-select" style="min-width:100px;">
                        @foreach(range(date('Y'), 2023) as $y)
                            <option value="{{ $y }}" {{ $an_sel == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="background:#1a5276;border-color:#1a5276;">
                        <i class="ti ti-search me-1"></i>Caută
                    </button>
                </div>
                @if(!empty($luniDisponibile))
                <div class="ms-auto">
                    <label class="form-label fw-semibold mb-1">Luni cu facturi</label>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($luniDisponibile as $ld)
                            <a href="{{ route('admin.facturi.index', ['luna' => $ld['luna'], 'an' => $ld['an']]) }}"
                               class="btn btn-sm {{ $luna_sel == $ld['luna'] && $an_sel == $ld['an'] ? 'btn-primary' : 'btn-outline-secondary' }}"
                               style="{{ $luna_sel == $ld['luna'] && $an_sel == $ld['an'] ? 'background:#1a5276;border-color:#1a5276;' : '' }}">
                                {{ $ld['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Statistici --}}
    @php
        $cuEmail   = collect($facturi)->filter(fn($f) => !empty($f['email']))->count();
        $faraEmail = collect($facturi)->filter(fn($f) => empty($f['email']))->count();
        $negasiti  = collect($facturi)->filter(fn($f) => $f['sursa'] === 'negasit')->count();
        $externi   = collect($facturi)->filter(fn($f) => $f['sursa'] === 'extern')->count();
        $portal    = collect($facturi)->filter(fn($f) => $f['sursa'] === 'portal')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#1a5276;">{{ count($facturi) }}</div>
                    <div class="text-muted small">Total facturi</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#27ae60;">{{ $cuEmail }}</div>
                    <div class="text-muted small">Cu email (vor primi)</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#e67e22;">{{ $negasiti }}</div>
                    <div class="text-muted small">Negăsiți în portal</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#8e44ad;">{{ $externi }}</div>
                    <div class="text-muted small">Clienți externi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Buton trimitere -- doar admin --}}
    @if($cuEmail > 0 && auth()->user()->hasRole('admin'))
    <div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #1a5276 !important;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.facturi.trimite') }}" id="sendForm">
                @csrf
                <input type="hidden" name="luna" value="{{ $luna_sel }}">
                <input type="hidden" name="an"   value="{{ $an_sel }}">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <strong>Gata de trimitere:</strong> <span id="selectedCount">{{ $cuEmail }}</span> mail-uri selectate pentru <strong>{{ $lunaText }}</strong>
                        @if($faraEmail > 0)
                            <span class="text-muted ms-2">({{ $faraEmail }} fără email — sărite)</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="selectAllBtn">
                            <i class="ti ti-checkbox me-1"></i>Selectează tot
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllBtn">
                            <i class="ti ti-square me-1"></i>Deselectează tot
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="sendBtn">
                            <i class="ti ti-send me-2"></i>Trimite selectate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Tabel preview --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a5276;color:#fff;">
            <span><i class="ti ti-list me-2"></i><strong>Preview – {{ $lunaText }}</strong></span>
            <div>
                <span class="badge bg-light text-dark me-1">Portal: {{ $portal }}</span>
                <span class="badge bg-light text-dark me-1">Externi: {{ $externi }}</span>
                <span class="badge bg-warning text-dark">Negăsiți: {{ $negasiti }}</span>
            </div>
        </div>
        <div class="card-body border-bottom py-2 px-3">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" id="searchFacturi" class="form-control form-control-sm"
                           placeholder="Caută după cod client, nume, email, nr. factură...">
                </div>
                <div class="text-muted small" id="searchInfo"></div>
            </div>
        </div>
        <div class="card-body p-0">
            @if(empty($facturi))
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-inbox" style="font-size:40px;"></i>
                    <p class="mt-2">Nu există facturi pentru <strong>{{ $lunaText }}</strong> în <code>storage/app/invoices/</code></p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;" id="facturiTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="checkAll" title="Selectează tot"></th>
                            <th>#</th>
                            <th>Cod Client</th>
                            <th>Nume</th>
                            <th>Email</th>
                            <th>Nr. Factură</th>
                            <th>Emisă</th>
                            <th>Scadență</th>
                            <th>Sursă</th>
                            <th>PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facturi as $i => $f)
                        <tr class="{{ $f['sursa'] === 'negasit' ? 'table-warning' : '' }}">
                            <td>
                                @if(!empty($f['email']))
                                    <input type="checkbox" name="selected[]" value="{{ $f['pdf_name'] }}"
                                           class="row-check" form="sendForm" checked>
                                @else
                                    <input type="checkbox" disabled title="Fără email">
                                @endif
                            </td>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $f['cod_client'] }}</strong></td>
                            <td>{{ $f['nume'] ?? '—' }}</td>
                            <td>
                                @if($f['email'])
                                    <small>{{ $f['email'] }}</small>
                                @else
                                    <span class="badge bg-danger">fără email</span>
                                @endif
                            </td>
                            <td>{{ $f['nr_factura'] }}</td>
                            <td>{{ $f['data_emitere'] }}</td>
                            <td>{{ $f['scadenta'] }}</td>
                            <td>
                                @if($f['sursa'] === 'portal')
                                    <span class="badge bg-primary">portal</span>
                                @elseif($f['sursa'] === 'extern')
                                    <span class="badge" style="background:#8e44ad;">extern</span>
                                @else
                                    <span class="badge bg-warning text-dark">negăsit</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $f['pdf_name'] }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>
</div>
</div>

<script>
// Selectează/deselectează tot
// Search în tabel
document.getElementById('searchFacturi')?.addEventListener('input', function() {
    const val = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#facturiTable tbody tr');
    let visible = 0;
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const show = !val || text.includes(val);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const total = rows.length;
    document.getElementById('searchInfo').textContent = val
        ? visible + ' din ' + total + ' rezultate'
        : '';
    updateCount();
});

document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateCount();
});

document.getElementById('selectAllBtn')?.addEventListener('click', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = true);
    document.getElementById('checkAll').checked = true;
    updateCount();
});

document.getElementById('deselectAllBtn')?.addEventListener('click', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('checkAll').checked = false;
    updateCount();
});

document.querySelectorAll('.row-check').forEach(cb => {
    cb.addEventListener('change', updateCount);
});

function updateCount() {
    const count = document.querySelectorAll('#facturiTable tbody tr:not([style*="display: none"]) .row-check:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

document.getElementById('sendForm')?.addEventListener('submit', function(e) {
    const count = document.querySelectorAll('.row-check:checked').length;
    if (count === 0) {
        e.preventDefault();
        alert('Selectează cel puțin o factură!');
        return;
    }
    if (!confirm('Trimiți ' + count + ' mail-uri? Acțiunea nu poate fi anulată.')) {
        e.preventDefault();
        return;
    }
    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Se trimit mail-urile...';
});
</script>
@endsection
