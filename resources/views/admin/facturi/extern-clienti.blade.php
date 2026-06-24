@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
<div class="row justify-content-center">
<div class="col-lg-10">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div style="background:#1a5276;border-radius:8px;padding:10px 14px;margin-right:14px;">
                <i class="ti ti-users" style="font-size:22px;color:#fff;"></i>
            </div>
            <div>
                <h4 class="mb-0" style="color:#1a5276;font-weight:700;">Clienți Externi</h4>
                <small class="text-muted">Clienți fără cont în portal care primesc factura pe email</small>
            </div>
        </div>
        <a href="{{ route('admin.facturi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Înapoi la facturi
        </a>
    </div>

    {{-- Mesaje --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('import_erori') && count(session('import_erori')) > 0)
        <div class="alert alert-warning">
            <strong>Avertismente import:</strong>
            <ul class="mb-0 mt-1">
                @foreach(session('import_erori') as $err)
                    <li><small>{{ $err }}</small></li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Coloana stânga: Adaugă manual + Import CSV --}}
        <div class="col-md-4">

            {{-- Adaugă manual --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background:#1a5276;color:#fff;">
                    <i class="ti ti-user-plus me-2"></i><strong>Adaugă client</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.extern-clienti.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cod Client</label>
                            <input type="text" name="cod_client" class="form-control @error('cod_client') is-invalid @enderror"
                                   placeholder="ex: 200037" value="{{ old('cod_client') }}" required>
                            @error('cod_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nr. Contract</label>
                            <input type="text" name="contract_nr" class="form-control @error('contract_nr') is-invalid @enderror"
                                   placeholder="ex: 5545" value="{{ old('contract_nr') }}" required>
                            @error('contract_nr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   placeholder="client@email.ro" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text mb-3">
                            <i class="ti ti-info-circle me-1"></i>Numele și client_id sunt luate automat din Oracle.
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background:#1a5276;border-color:#1a5276;">
                            <i class="ti ti-plus me-1"></i>Adaugă
                        </button>
                    </form>
                </div>
            </div>

            {{-- Import CSV --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <i class="ti ti-upload me-2"></i><strong>Import CSV bulk</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.extern-clienti.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <div class="form-text mb-3">
                            Coloane: <code>cod_client, contract_nr, email</code><br>
                            Separator: <code>,</code> sau <code>;</code>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="ti ti-file-import me-1"></i>Importă
                        </button>
                    </form>

                    {{-- Exemplu CSV --}}
                    <button class="btn btn-link btn-sm p-0 mt-2" data-bs-toggle="collapse" data-bs-target="#csvEx">
                        Vezi exemplu CSV
                    </button>
                    <div class="collapse mt-2" id="csvEx">
                        <pre class="bg-light p-2 rounded" style="font-size:11px;">cod_client,contract_nr,email
200037,5545,parohie@email.ro
200038,6123,firma@email.ro</pre>
                    </div>
                </div>
            </div>

        </div>

        {{-- Coloana dreapta: Listă clienți --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a5276;color:#fff;">
                    <span><i class="ti ti-list me-2"></i><strong>Clienți externi ({{ $clienti->total() }})</strong></span>
                </div>
                <div class="card-body border-bottom py-2 px-3">
                    <form method="GET" action="{{ route('admin.extern-clienti.index') }}" class="d-flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm"
                               placeholder="Caută după cod client, nume, email...">
                        <button type="submit" class="btn btn-sm btn-primary" style="background:#1a5276;border-color:#1a5276;">
                            <i class="ti ti-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.extern-clienti.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ti ti-x"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="card-body p-0">
                    @if($clienti->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-users-off" style="font-size:36px;"></i>
                            <p class="mt-2">Niciun client extern adăugat încă.</p>
                        </div>
                    @else
                    <div class="table-responsive" style="max-height:600px;overflow-y:auto;">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Cod Client</th>
                                    <th>Nume</th>
                                    <th>Email</th>
                                    <th>Contract</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clienti as $c)
                                <tr>
                                    <td><strong>{{ $c->cod_client }}</strong></td>
                                    <td>{{ $c->nume }}</td>
                                    <td>
                                        {{-- Edit email inline --}}
                                        <form method="POST" action="{{ route('admin.extern-clienti.update', $c) }}"
                                              class="d-flex gap-1" style="min-width:200px;">
                                            @csrf @method('PUT')
                                            <input type="email" name="email" value="{{ $c->email }}"
                                                   class="form-control form-control-sm" required>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Salvează">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><small class="text-muted">{{ $c->contract_nr }}</small></td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.extern-clienti.destroy', $c) }}"
                                              onsubmit="return confirm('Ștergi clientul {{ $c->nume }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Șterge">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $clienti->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>
@endsection
