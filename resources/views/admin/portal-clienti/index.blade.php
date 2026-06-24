@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
<div class="row justify-content-center">
<div class="col-lg-8">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div style="background:#1a5276;border-radius:8px;padding:10px 14px;margin-right:14px;">
                <i class="ti ti-database-export" style="font-size:22px;color:#fff;"></i>
            </div>
            <div>
                <h4 class="mb-0" style="color:#1a5276;font-weight:700;">Export Clienți Portal</h4>
                <small class="text-muted">Toți utilizatorii înregistrați cu codurile lor de client</small>
            </div>
        </div>
    </div>

    {{-- Statistici --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#1a5276;">{{ number_format($totalUseri) }}</div>
                    <div class="text-muted small">Utilizatori totali</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#1a5276;">{{ number_format($totalCoduri) }}</div>
                    <div class="text-muted small">Coduri de client</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#27ae60;">{{ number_format($cuEmail) }}</div>
                    <div class="text-muted small">Cu email</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#27ae60;">{{ number_format($verificati) }}</div>
                    <div class="text-muted small">Email verificat</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#8e44ad;">{{ number_format($notifFactura) }}</div>
                    <div class="text-muted small">Notif. factură activă</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#e67e22;">{{ number_format($faraContPlatit) }}</div>
                    <div class="text-muted small">Cont inactiv/neverificat</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#c0392b;">{{ number_format($totalExterni) }}</div>
                    <div class="text-muted small">Clienți externi</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div style="font-size:32px;font-weight:700;color:#1a5276;">{{ number_format($totalToti) }}</div>
                    <div class="text-muted small">Total clienți (portal + externi)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Export --}}
    <div class="row g-4">

        {{-- Export clienți portal --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:#1a5276;color:#fff;">
                    <i class="ti ti-file-spreadsheet me-2"></i><strong>Clienți Portal</strong>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        <strong>{{ number_format($totalCoduri) }} rânduri</strong> — un rând per cod de client.<br>
                        Include toate coloanele din <code>users</code> + <code>client_codes</code>.
                    </p>
                    <div class="mb-3 d-flex flex-wrap gap-1">
                        @foreach(['User ID', 'Nume', 'Email', 'Telefon', 'Status', 'Categorie', 'Notif. Email', 'Notif. SMS', 'Notif. Factură', 'Email Verificat', 'Înregistrat La', 'Cod Client', 'Nr. Contract', 'Client ID'] as $col)
                            <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $col }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.portal-clienti.export') }}"
                       class="btn btn-success mt-auto"
                       style="background:#27ae60;border-color:#27ae60;">
                        <i class="ti ti-download me-2"></i>Descarcă Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- Export clienți externi --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:#8e44ad;color:#fff;">
                    <i class="ti ti-file-spreadsheet me-2"></i><strong>Clienți Externi</strong>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        <strong>{{ number_format($totalExterni) }} rânduri</strong> — clienți fără cont în portal.<br>
                        Include codurile, numele, email-ul și contractul.
                    </p>
                    <div class="mb-3 d-flex flex-wrap gap-1">
                        @foreach(['Cod Client', 'Nume', 'Email', 'Nr. Contract', 'Client ID', 'Adăugat La'] as $col)
                            <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $col }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.portal-clienti.export-externi') }}"
                       class="btn btn-success mt-auto"
                       style="background:#8e44ad;border-color:#8e44ad;">
                        <i class="ti ti-download me-2"></i>Descarcă Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- Export toți clienții --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:#c0392b;color:#fff;">
                    <i class="ti ti-file-spreadsheet me-2"></i><strong>Toți Clienții</strong>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        <strong>{{ number_format($totalToti) }} rânduri</strong> — portal + externi combinat.<br>
                        Coloana <strong>Sursă</strong> indică originea fiecărui rând.
                    </p>
                    <div class="mb-3 d-flex flex-wrap gap-1">
                        @foreach(['Cod Client', 'Nume', 'Email', 'Telefon', 'Nr. Contract', 'Client ID', 'Sursă'] as $col)
                            <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $col }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.portal-clienti.export-toti') }}"
                       class="btn btn-success mt-auto"
                       style="background:#c0392b;border-color:#c0392b;">
                        <i class="ti ti-download me-2"></i>Descarcă Excel
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
</div>
</div>
@endsection
