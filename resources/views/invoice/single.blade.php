@extends('layouts.app')
@section('title', trans('general.pages.invoice.title'))
@section('content')
    <div id="content-main" class="single-invoice">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.invoice.invoice'): {{ $data['numarfactura'] }}</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-md-4 mb-4">
                    <div class="bg-white shadow-sm box-container h-100">
                        <h4 class="mb-3">@lang('general.pages.invoice.payment_details')</h4>
                        <p><strong>@lang('general.pages.invoice.invoice_id'):</strong> {{ $data['numarfactura'] }}</p>
                        <p><strong>@lang('general.pages.invoice.payment_total'):</strong> {{ $data['rest_icpl'] }} RON
                        </p>

                    </div>
                </div>
                <div class="col-12 col-md-4 mb-4">
                    <div class="bg-white shadow-sm box-container h-100">
                        <h4 class="mb-3">@lang('general.pages.invoice.invoice_data')</h4>
                        <p><strong>@lang('general.pages.invoice.invoice_issuance_date')
                                :</strong> {{ (new \Carbon\Carbon($data['datafactura']))->format('d.m.Y') }}</p>
                        <p><strong>@lang('general.pages.invoice.due_date')
                                :</strong> {{ (new \Carbon\Carbon($data['scadenta']))->format('d.m.Y')  }}</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-4">
                    <div class="bg-white shadow-sm box-container h-100">
                        <h4 class="mb-3">@lang('general.pages.invoice.client_code')
                            : {{ auth()->user()->codes[0]['client_code'] }}</h4>
                        <p><strong>@lang('general.pages.invoice.consumption_location')
                                :</strong> {{ $location['addr_text'] }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <h4 class="mb-3">@lang('general.pages.invoice.tax_details')</h4>
                        <div class="table-responsive">
                            <table id="invoicesTable" class="table table-actions" style="width:100%"
                                   data-invoiceId="{{ $data['numarfactura'] }}">
                                <thead>
                                <tr>
                                    <th>@lang('labels.nr')</th>
                                    <th>@lang('labels.unit_code')</th>
                                    <th>@lang('labels.quantity')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.value')</th>
                                    <th>@lang('labels.vat')</th>
                                    <th>@lang('labels.total_value')</th>
                                    <th>@lang('labels.rest')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoice as $unit)
                                    <tr>
                                        <td>{{ $unit['pozitie'] }}</td>
                                        <td>{{ $unit['codprestatie'] }}</td>
                                        <td>{{ $unit['cant'] }}</td>
                                        <td>{{ $unit['pret'] }}</td>
                                        <td>{{ $unit['val'] }}</td>
                                        <td>{{ $unit['tva'] }}</td>
                                        <td>{{ $unit['valtotal'] }}</td>
                                        <td>{{ $unit['rest_icpl'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if($data['rest_icpl'])
                                <form method="post" action="{{ route('invoice.pay') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="invoicePay[]" value="{{ $data['numarfactura'] }}"/>
                                    <button type="submit" class="btn btn-primary d-table ml-auto mt-4">
                                        @lang('general.pages.invoice.pay') {{ $data['rest_icpl'] }} RON
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
