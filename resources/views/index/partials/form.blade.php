@if(!$isReady)
    <?php echo trans('general.pages.index.index_period', ['date_from' => $period['from'] . '.' . date('m.Y'), 'date_to' => $period['to'] . '.' . date('m.Y')]); ?>
@else
    <div class="last-index" style="display:{{ !$hasMeter ? 'none' : 'block' }}">
        <p>@lang('general.pages.index.index_form')</p>
        <p>@lang('general.pages.index.last_index')
            <span class="font-weight-500">
            @if(!empty($currentLocation['currentWaterMeter']['indexes']))
                    {{ $currentLocation['currentWaterMeter']['indexes'][0]['index_nou'] ?: $currentLocation['currentWaterMeter']['indexes'][0]['index_vechi'] }}
                @else
                    0
                @endif
         </span>
        </p>
    </div>

    <span style="display:{{ $hasMeter ? 'none' : 'block' }}"> @lang('general.pages.index.no_waterMeters')</span>
    @if(empty($currentLocation['currentWaterMeter']['indexes']))
        <p>Nu sunt introduse datele apometrului. Va rog contactati operatorul la tel: 0240.511.369 sau <a
                target="_blank" href="mailto:contractare.facturare@aquaservtulcea.ro">contractare.facturare@aquaservtulcea.ro</a></p>
@elseif(!$currentLocation['currentWaterMeter']['indexes'][0]['index_nou'] || ( $currentLocation['currentWaterMeter']['indexes'][0]['index_nou'] && $currentLocation['currentWaterMeter']['indexes'][0]['prov_valoare'] == 'MYAPA'))
        <form class="one-row-form mt-4" method="post" action="{{ route('index.store') }}">
            {{ csrf_field() }}
            <div class="row align-items-end">
                <div class="col-8">
                    <div class="md-input-group">
                        <input {{ !$hasMeter ? 'disabled' : '' }} type="text"
                               placeholder="@lang('general.pages.index.index_date') {{ date('d-m-Y') }}"
                               id="send-index" name="value"/>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label for="send-index">@lang('general.pages.index.index_date') {{ date('d-m-Y') }}</label>
                        @if ($errors->has('value'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('value') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="invalid-feedback d-block">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="valid-feedback d-block">
                                <button class="close" data-close="alert"></button>
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <button type="submit" {{ !$hasMeter ? 'disabled' : '' }} class="btn btn-primary">
                        @lang('general.pages.index.send_index')
                    </button>
                </div>
            </div>
        </form>
 @else
        <p>Stimate client,</p>
        <br/>
        <p> Vă informăm că nu putem prelua citirea dumneavoastră deoarece indexul contorului de apă a fost deja citit în
            această lună de personalul Aquaserv.
            <br/>
        <p>Vă mulțumim,<br/>
            Aquaserv </p>
    @endif
@endif

