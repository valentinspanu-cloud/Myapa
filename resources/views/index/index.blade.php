@extends('layouts.app')
@section('title', trans('general.pages.index.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.index.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                @if(!empty($locations))
                    <div class="col-12 col-lg-6">
                        <!-- -->
                        <div class="bg-white shadow-sm box-container">
                            <label for="cont-consum-facturi"
                                   class="box-container__title mb-2">Cod client</label>
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
                        <!-- -->
                        <div class="bg-white shadow-sm box-container">
                            <label for="locatie-consum"
                                   class="box-container__title mb-2">@lang('labels.locations')</label>
                            <select class="custom-select" id="locatie-consum">
                                @foreach($locations as $location)
                                    <option {{ $location['cod_loc'] == $currentLocation['cod_loc'] ? 'selected' : '' }}
                                            value="{{ $location['cod_loc'] }}">{{ $location['addr_text'] }}</option>
                                @endforeach
                            </select>
                            <div class="w-100 my-3"></div>
                            <label for="contor"
                                   class="box-container__title mb-2">@lang('labels.waterMeters')</label>
                            <select class="custom-select" id="contor"
                                    style="display: {{ empty($currentLocation['currentWaterMeter']) ? 'none' : 'block'  }};">
                                @if(!empty($waterMeters))
                                    @foreach($waterMeters as $waterMeter)
                                        <option
                                            {{ $waterMeter['cod_contor'] == $currentLocation['currentWaterMeter']['cod_contor'] ? 'selected' : '' }} value="{{ $waterMeter['cod_contor'] }}">
                                            {{ $waterMeter['cod_contor'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="no-waterMeters"
                                  style="display: {{ !empty($currentLocation['currentWaterMeter']) ? 'none' : 'block'  }};">
                            @lang('general.pages.index.no_waterMeters')
                        </span>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="bg-white shadow-sm box-container send-index">
                            @include('index.partials.form', ['period' => $period])
                        </div>
                    </div>
                @else
                    <div class="col-12 col-lg-6">
                        <div class="bg-white shadow-sm box-container">
                            <p>@lang('general.pages.index.no_locations')</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <div class="box-container__title__w-filter">
                            <h4>@lang('general.pages.index.history')</h4>
                        </div>
                        <div class="box-container__title__w-filter">
                            <div class="filter-form">
                                <label for="start-date">Data citirii intre</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="from" id="start-date"/>
                                <label for="end-date">si</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="to" id="end-date" />
                                <input type="submit" value="Filtrează"
                                       class="btn btn-outline-secondary btn-filter"/>
                                <input type="submit" value="Resetează" class="btn btn-link btn-reset"/>
                            </div>
                        </div>
                        <div class="index-list">
                            @include('index.partials.list')
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
    <script src="{{ asset('js/range_dates.js') }}" ></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}" ></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}" ></script>
@endsection
