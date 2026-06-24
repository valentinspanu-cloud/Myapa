@if(!empty($payedInvoices))
    <div class="table-responsive">
        <div class="box-container__title__w-filter">
            <div class="filter-form">
                <label for="start-date">Data facturarii intre</label>
                <input type="text" data-date-end-date="0d" class="form-control" name="from" id="start-date"/>
                <label for="end-date">si</label>
                <input type="text" data-date-end-date="0d" class="form-control" name="to" id="end-date"/>
                <input type="submit" value="Filtrează"
                       class="btn btn-outline-secondary btn-filter"/>
                <input type="submit" value="Resetează" class="btn btn-link btn-reset"/>
            </div>
        </div>
        <table id="invoiceTable" class="table custom-table" style="width:100%">
            <thead>
            <tr>
                <th>@lang('labels.nr')</th>
                <th>@lang('labels.date')</th>
                <th>@lang('labels.due_date')</th>
                <th>@lang('labels.value')</th>
                <th>@lang('labels.payed')</th>
                <th>@lang('labels.rest')</th>
		<th>Tip plata</th>
                <th>@lang('labels.view')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payedInvoices as $invoice)
                <tr>
                    <td>{{ $invoice['numarfactura'] }}</td>
                    <td>{{ date('Y-m-d', strtotime($invoice['datafactura'])) }}</td>
                    <td>{{ date('Y-m-d', strtotime($invoice['scadenta'])) }}</td>
                    <td>{{ $invoice['valtotal'] }}</td>
                    <td>{{ $invoice['valtotal'] - $invoice['rest_icpl'] }}</td>
                    <td>{{ $invoice['rest_icpl'] }}</td>
		   <td>{{ paymentType($invoice['idfactura']) }}</td>
                    <td>
                        <a href="{{ route('invoice.single', $invoice['idfactura']) }}" class="hover-to-accent"
                           title="Vezi factura">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>

                        @if(invoiceExists($invoice['numarfactura'], $invoice['datafactura']))
                            <a href="{{ route('invoice.pdf', ['id' => str_replace('/','',$invoice['numarfactura']), 'date' => date('dmY', strtotime($invoice['datafactura']))]) }}"
                               
                               class="hover-to-accent" title="Descarcă factura">
                                <i class="far fa-file-pdf" aria-hidden="true"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    @lang('general.pages.invoice.no_invoices')
@endif
