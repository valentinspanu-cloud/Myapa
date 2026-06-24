@if(!empty($currentLocation['currentWaterMeter']['indexes']))
    <div class="table-responsive">
        <table id="indexTable" class="table custom-table" style="width:100%">
            <thead>
            <tr>
                <th>@lang('labels.client_code')</th>
                <th>Luna</th>
                <th>@lang('labels.date') citire</th>
                <th>@lang('labels.index')</th>
                <th>@lang('labels.source')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($currentLocation['currentWaterMeter']['indexes'] as $index)
                @if(!$index['index_nou'])
                    @continue
                @endif
                <tr>
                    <td>{{ $user->codes[0]->client_code }}</td>
                    <td>{{ date('M-Y', strtotime($index['luna_de_citiri'])) }}</td>
                    <td>{{ date('d-m-Y', strtotime($index['data_sf'])) }}</td>
                    <td>{{ $index['index_nou'] }}</td>
                    <td>{{ session('types')[$index['prov_valoare']] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    @lang('general.pages.index.no_indexes')
@endif
