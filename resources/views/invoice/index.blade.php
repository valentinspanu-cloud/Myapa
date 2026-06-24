@extends('layouts.app')
@section('title', trans('general.pages.invoice.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.invoice.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-6">
                    <div class="bg-white shadow-sm box-container">
                        <label for="cont-consum-facturi" class="box-container__title mb-2">Cod client</label>
                        <form method="POST" action="{{ route('users.changeLoc', auth()->user()->id) }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT"/>
                            <select class="custom-select" name="alege" id="cont-consum-facturi">
                                @if(!empty(session('locationsAll')))
                                    @foreach(session('locationsAll') as $location)
                                        <option {{ $location['cod_client'] == auth()->user()->codes[0]['client_code'] ? 'selected' : '' }}
                                                value="{{ $location['cod_client'] }}">{{ $location['cod_client'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </form>
                        <div class="w-100 my-3"></div>
                    </div>
                    <div class="bg-white shadow-sm box-container">
                        <label for="locatie-consum-facturi" class="box-container__title mb-2">@lang('labels.locations')</label>
                        <form method="post" action="{{ route('invoice.history') }}">
                            {{ csrf_field() }}
                            <select class="custom-select" name="currentLocation" id="locatie-consum-facturi">
                                @if(!empty($locations))
                                    @foreach($locations as $location)
                                        <option {{ $location['cod_loc'] == $currentLocation['cod_loc'] ? 'selected' : '' }}
                                                value="{{ $location['cod_loc'] }}">{{ $location['addr_text'] == '' ? $location['cod_loc']: $location['addr_text'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </form>
                        <div class="w-100 my-3"></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-white shadow-sm box-container">
                        <label class="box-container__title mb-2">Sold curent:</label>
                        <div class="w-100" style="margin-bottom: 29px !important;">{{ $sold }} RON</div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                @if(session()->has('message'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <span> {{ session()->get('message') }} </span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('labels.close')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <label class="box-container__title mb-2">@lang('general.pages.invoice.unpayed')</label>
                        <div class="table-responsive">
                            @if(!empty($unPayedInvoices))
                                <form method="post" action="{{ route('invoice.pay') }}">
                                    {{ csrf_field() }}
                                    <table id="selectable-table" class="table table-actions" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>@lang('labels.nr')</th>
                                            <th>@lang('labels.date')</th>
                                            <th>@lang('labels.due_date')</th>
                                            <th>@lang('labels.value')</th>
                                            <th>@lang('labels.payed')</th>
                                            <th>@lang('labels.rest')</th>
                                            <th>@lang('labels.view')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($unPayedInvoices as $invoice)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="invoicePay" name="invoicePay[]"
                                                           data-value="{{ $invoice['rest_icpl'] }}"
                                                           value="{{ $invoice['numarfactura'] }}"/>
                                                </td>
                                                <td>{{ $invoice['numarfactura'] }}</td>
                                                <td>{{ date('d.m.Y', strtotime($invoice['datafactura'])) }}</td>
                                                <td>{{ date('d.m.Y', strtotime($invoice['scadenta'])) }}</td>
                                                <td>{{ $invoice['valtotal'] }}</td>
                                                <td>{{ $invoice['valtotal'] - $invoice['rest_icpl'] }}</td>
                                                <td>{{ $invoice['rest_icpl'] }}</td>
                                                <td>
                                                    <a href="{{ route('invoice.single', $invoice['idfactura']) }}"
                                                       class="hover-to-accent" title="Vezi factura">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    @if(invoiceExists($invoice['numarfactura'], $invoice['datafactura']))
                                                        <a target="_blank" class="hover-to-accent"
                                                           title="Descarca factura"
                                                           href="{{ route('invoice.pdf', ['id' => str_replace('/', '', $invoice['numarfactura']), 'date' => date('dmY', strtotime($invoice['datafactura']))]) }}">
                                                            <i class="far fa-file-pdf" aria-hidden="true"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="paysafe">
                                        <button type="submit"
                                                data-no-pay="@lang('general.pages.invoice.select_invoice')"
                                                data-pay="@lang('general.pages.invoice.pay')"
                                                class="btn btn-primary payInvoice"
                                                disabled="disabled">
                                            @lang('general.pages.invoice.select_invoice')
                                        </button>
                                        <img style="max-height: 36px;" src="{{ asset('img/paylogo.jpg') }}"/>
                                    </div>
                                </form>
                            @else
                                <p>@lang('general.pages.invoice.no_unpayed_invoices')</p>
                                <div class="mt-4">
                                    <p class="text-muted mb-3">Puteti efectua o plata in avans in contul dumneavoastra:</p>
                                    <form method="POST" action="{{ route('invoice.payAdvance') }}">
                                        {{ csrf_field() }}
                                        <div class="d-flex align-items-center" style="gap:10px;max-width:400px">
                                            <div class="input-group">
                                                <input type="number"
                                                       name="advance_amount"
                                                       class="form-control"
                                                       placeholder="Suma avans (RON)"
                                                       min="1"
                                                       step="0.01"
                                                       required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">RON</span>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" style="white-space:nowrap">
                                                Plata avans
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <div class="box-container__title__w-filter">
                            <h4>@lang('general.pages.invoice.history')</h4>
                        </div>
                        <div id="history-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Se incarca...</span>
                            </div>
                            <p class="mt-2 text-muted small">Se incarca istoricul facturilor...</p>
                        </div>
                        <div id="history-table-wrap" style="display:none">
                            <div class="table-responsive">
                                <table class="table table-actions" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.nr')</th>
                                        <th>@lang('labels.date')</th>
                                        <th>@lang('labels.due_date')</th>
                                        <th>@lang('labels.value')</th>
                                        <th>@lang('labels.view')</th>
                                    </tr>
                                    </thead>
                                    <tbody id="history-body"></tbody>
                                </table>
                            </div>
                            <div id="history-pagination" class="d-flex justify-content-center flex-wrap mt-3" style="gap:4px"></div>
                        </div>
                        <div id="history-empty" style="display:none">
                            <p class="text-muted">@lang('general.pages.invoice.no_payed_invoices')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('header')
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('footer')
    <script src="{{ asset('js/range_dates.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}"></script>
    <script>
    (function () {
        var ROUTE = "{{ route('invoice.history.ajax') }}";
        var csrfToken = document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : '';
        function fmt(d) {
            if (!d) return '-';
            return d.substring(0, 10).split('-').reverse().join('.');
        }
        function renderRows(data) {
            if (!data.length) return '';
            return data.map(function (inv) {
                var viewUrl = "{{ url('factura') }}/" + inv.idfactura;
                var dateFmt = inv.datafactura ? inv.datafactura.substring(0,10).replace(/-/g,'') : '';
                var nrClean = inv.numarfactura ? inv.numarfactura.replace(/\//g, '') : '';
                var pdfUrl  = "{{ url('facturi/descarca-factura') }}/" + nrClean + '/' + dateFmt;
                var pdfBtn  = inv.has_pdf
                    ? ' <a target="_blank" class="hover-to-accent" title="Descarca factura" href="' + pdfUrl + '"><i class="far fa-file-pdf" aria-hidden="true"></i></a>'
                    : '';
                return '<tr>' +
                    '<td>' + inv.numarfactura + '</td>' +
                    '<td>' + fmt(inv.datafactura) + '</td>' +
                    '<td>' + fmt(inv.scadenta) + '</td>' +
                    '<td>' + inv.valtotal + '</td>' +
                    '<td><a href="' + viewUrl + '" class="hover-to-accent" title="Vezi factura">' +
                        '<i class="fa fa-eye" aria-hidden="true"></i></a>' + pdfBtn + '</td>' +
                    '</tr>';
            }).join('');
        }
        function renderPagination(current, last) {
            if (last <= 1) return '';
            var html = '';
            html += '<button class="btn btn-sm btn-outline-secondary page-btn" data-page="1"' +
                    (current === 1 ? ' disabled' : '') + '>&laquo;</button>';
            var from = Math.max(1, current - 2);
            var to   = Math.min(last, current + 2);
            if (from > 1) html += '<span class="btn btn-sm disabled">&hellip;</span>';
            for (var i = from; i <= to; i++) {
                html += '<button class="btn btn-sm ' + (i === current ? 'btn-primary' : 'btn-outline-secondary') +
                        ' page-btn" data-page="' + i + '">' + i + '</button>';
            }
            if (to < last) html += '<span class="btn btn-sm disabled">&hellip;</span>';
            html += '<button class="btn btn-sm btn-outline-secondary page-btn" data-page="' + last + '"' +
                    (current === last ? ' disabled' : '') + '>&raquo;</button>';
            return html;
        }
        function loadHistory(page) {
            fetch(ROUTE + '?page=' + page, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                document.getElementById('history-loading').style.display = 'none';
                if (!res.total) {
                    document.getElementById('history-empty').style.display = 'block';
                    return;
                }
                document.getElementById('history-body').innerHTML      = renderRows(res.data);
                document.getElementById('history-pagination').innerHTML = renderPagination(res.page, res.lastPage);
                document.getElementById('history-table-wrap').style.display = 'block';
            })
            .catch(function () {
                document.getElementById('history-loading').innerHTML =
                    '<p class="text-danger">Eroare la incarcarea istoricului. Reincarcati pagina.</p>';
            });
        }
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('page-btn') && !e.target.disabled) {
                var page = parseInt(e.target.getAttribute('data-page'), 10);
                document.getElementById('history-table-wrap').style.display = 'none';
                document.getElementById('history-loading').style.display    = 'block';
                loadHistory(page);
                setTimeout(function () {
                    document.getElementById('history-loading').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 50);
            }
        });
        loadHistory(1);
    })();
    </script>
@endsection
