@extends('layouts.login')
@section('title', trans('general.pages.register.title'))
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12 col-md-1">
            </div>
            <div class="col-12 col-md-10">
                <div class="main-box py-5 px-4">
                    <h1 class="text-center mb-5">@lang('general.pages.register.title')</h1>
                    <form class="sign-form mx-auto" method="POST" action="{{ route('register') }}">
                        @if(session('siverror'))
                            <div class="md-input-group">
                                <div class="invalid-feedback d-block">
                                    {{ session('siverror') }}
                                </div>
                            </div>
                        @endif
                        {{ csrf_field() }}
                        <div class="md-input-group">
                            <input type="text" id="cod-client" value="{{ old('client_code') }}" name="client_code"
                                   placeholder="@lang('labels.client_code')"/>
			    <a href="#" class="tooltipc"><i style="color:#ff8f00;" class="fas fa-info-circle"></i>
                                <span><img src="/img/cod_client.jpg" style="float:right;" /></span>
                            </a> 
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="cod-client">@lang('labels.client_code')</label>
                            @if ($errors->has('client_code'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('client_code') }}
                                </div>
                            @endif
                        </div>
                        <div class="md-input-group">
                            <input type="text" id="nr-contract" value="{{ old('contract_nr') }}"
                                   placeholder="@lang('labels.contract_nr')"
                                   name="contract_nr"/>
			    <a href="#" class="tooltipc"><i style="color:#ff8f00;" class="fas fa-info-circle"></i>
                                <span><img src="/img/numar_contract.jpg" style="float:right;" /></span>
                            </a> 
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="nr-contract">@lang('labels.contract_nr')</label>
                            @if ($errors->has('contract_nr'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('contract_nr') }}
                                </div>
                            @endif
                        </div>
                        <div class="md-input-group">
                            <input type="email" id="email" value="{{ old('email') }}" name="email"
                                   placeholder="@lang('labels.email')"/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="email">@lang('labels.email')</label>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>
                        <div class="md-input-group">
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   placeholder="@lang('labels.phone')"/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="email">@lang('labels.phone')</label>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>
                        <div class="md-input-group">
                            <input type="password" id="password" autocomplete="off" name="password"
                                   placeholder="@lang('general.pages.register.password_label')"/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="password">@lang('general.pages.register.password_label')</label>
                            @if ($errors->has('password'))
                                <div class="invalid-feedback d-block">
                                    {!! $errors->first('password') !!}
                                </div>
                            @endif
                        </div>

			 <div id="pswd_info">
                                <h4>Parola trebuie sÄƒ conÈ›inÄƒ:</h4>
                                <ul>
                                    <li id="letter" class="invalid">minim <strong>un caracter special</strong></li>
                                    <li id="capital" class="invalid">minim <strong>un caracter mare</strong></li>
                                    <li id="number" class="invalid">minim <strong>o cifrÄƒ</strong></li>
                                    <li id="length" class="invalid">minim <strong>6 caractere</strong></li>
                                </ul>
                            </div>
                        <div class="md-input-group">
                            <input type="password" id="repeat-password" autocomplete="off" name="password_confirmation"
                                   placeholder="@lang('general.pages.register.confirm_label')"
                                   value=""/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="repeat-password">@lang('general.pages.register.confirm_label')</label>
			    <span id="pswdInfoRepeat">Parolele nu sunt identice </span>
                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>
                        <div class="custom-control custom-checkbox agree">
                            <input type="checkbox" id="agree"
                                   name="agree" value="1" class="custom-control-input"/>
                            <label class="custom-control-label"
                                   for="agree">{!!  trans('labels.agree', ['gdpr' => route('cms.view', getPage(5)['slug'])]) !!}</label>
                            @if ($errors->has('agree'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('agree') }}
                                </div>
                            @endif
                        </div>
                        <div id="login_id"></div>
                        {!!  GoogleReCaptchaV3::renderOne('login_id', 'login_id') !!}
                        <div class="custom-recaptcha">
                            Acest website este protejat de reCAPTCHA v3 invisible È™i vizitatorilor li se aplicÄƒ
                            <a target="_blank" href="https://policies.google.com/privacy"> Politica de
                                confidenÈ›ialitate</a> È™i
                            <a target="_blank" href="https://policies.google.com/terms">Termenii de utilizare</a>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mx-auto d-block my-5">
                            @lang('general.pages.register.register_btn')
                        </button>
                        <div class="d-flex justify-content-end my-5">
                            <a class="primary-link"
                               href="{{ route('login') }}">@lang('general.pages.register.login_btn')</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-1">
            </div>
        </div>
    </div>

@endsection