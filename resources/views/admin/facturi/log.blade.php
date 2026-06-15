@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
<div class="row justify-content-center">
<div class="col-lg-11">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div style="background:#1a5276;border-radius:8px;padding:10px 14px;margin-right:14px;">
                <i class="ti ti-history" style="font-size:22px;color:#fff;"></i>
            </div>
            <div>
                <h4 class="mb-0" style="color:#1a5276;font-weight:700;">Log Mailuri Facturi</h4>
                <small class="text-muted">Istoricul tuturor mail-urilor trimise</small>
            </div>
        </div>
        <a href="{{ route('admin.facturi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Înapoi la facturi
        </a>
    </div>

    {{-- Statistici --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#1a5276;">{{ number_format($totalTrimise + $totalEsuate) }}</div>
                    <div class="text-muted small">Total procesate</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#27ae60;">{{ number_format($totalTrimise) }}</div>
                    <div class="text-muted small">Trimise cu succes</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:28px;font-weight:700;color:#e74c3c;">{{ number_format($totalEsuate) }}</div>
                    <div class="text-muted small">Erori</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.facturi.log') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" style="max-width:300px;"
                       placeholder="Caută cod client, email, nr. factură...">
                <select name="status" class="form-select form-select-sm" style="max-width:150px;">
                    <option value="">Toate</option>
                    <option value="trimis"  {{ request('status') == 'trimis'  ? 'selected' : '' }}>✓ Trimise</option>
                    <option value="eroare"  {{ request('status') == 'eroare'  ? 'selected' : '' }}>✗ Erori</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary" style="background:#1a5276;border-color:#1a5276;">
                    <i class="ti ti-search me-1"></i>Caută
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.facturi.log') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-x me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a5276;color:#fff;">
            <span><i class="ti ti-list me-2"></i><strong>Log ({{ $logs->total() }} înregistrări)</strong></span>
        </div>
        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-inbox" style="font-size:40px;"></i>
                    <p class="mt-2">Nu există înregistrări.</p>
                </div>
            @else
            <div class="table-responsive" style="max-height:600px;overflow-y:auto;">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                        <tr>
                            <th>#</th>
                            <th>Cod Client</th>
                            <th>Nr. Factură</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Eroare</th>
                            <th>Trimis La</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $i => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $i }}</td>
                            <td><strong>{{ $log->cod_client }}</strong></td>
                            <td>{{ $log->nr_factura }}</td>
                            <td><small>{{ $log->email }}</small></td>
                            <td>
                                @if($log->status === 'trimis')
                                    <span class="badge bg-success">✓ trimis</span>
                                @else
                                    <span class="badge bg-danger">✗ eroare</span>
                                @endif
                            </td>
                            <td><small class="text-danger">{{ $log->eroare ?? '' }}</small></td>
                            <td><small class="text-muted">{{ $log->trimis_la }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3" style="font-size:13px;">
                {{ $logs->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>
</div>
@endsection
