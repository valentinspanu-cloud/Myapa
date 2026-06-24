@extends('layouts.app')
@section('title', trans('general.pages.settings.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.settings.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-lg-12">
                    <div class="bg-white shadow-sm box-container">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form method="post" class="custom-form--input-spacing" action="{{ route('settings.update') }}">
                            {{ csrf_field() }}
                            @foreach($settings as $setting)
                                @if($setting->key != 'bank')
                                    <div class="md-input-group">
                                        <input type="text" name="key[{{ $setting->key }}]" id="key[{{ $setting->key }}]"
                                               value="{{ $setting->value }}"
                                               placeholder="@lang('labels.name')"/>
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label for="name">{{ $setting->label }}</label>
                                        <small id="key[{{ $setting->key }}]"
                                               class="form-text text-muted">{{ $setting->comment }}</small>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="key[bank]">@lang('labels.bank')</label>
                                        <select class="form-control" name="key[bank]" id="key[bank]" required>
                                            @foreach($accounts as $account)
                                                <option
                                                    value="{{ implode('||', $account) }}"
                                                    {{ explode('||', $setting->value)[0] == $account['banc_val_pk'] ? 'selected' : '' }}>
                                                    {{ $account['cod_b'] . ' - ' . $account['cont_valut'] . ( $account['den_cont'] ? ' - ' . $account['den_cont'] : '') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                            @endforeach
                            <button type="submit" class="btn btn-primary d-table ml-auto my-3">
                                @lang('labels.save')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
