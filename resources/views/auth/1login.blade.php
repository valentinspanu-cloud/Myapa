@extends('layouts.login')
@section('title', trans('general.pages.login.title'))
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('labels.close')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12 col-md-1">
            </div>
            <div class="col-12 col-md-10">
                <div class="main-box py-5 px-4">
                    <h1 class="text-center mb-5">@lang('general.pages.login.title')</h1>
                    <form class="sign-form mx-auto" method="POST" action="{{ route('login') }}">
                        <div class="form-group">
                            @if (Request::has('previous'))
                                <input type="hidden" name="previous" value="{{ Request::get('previous') }}">
                            @else
                                <input type="hidden" name="previous" value="{{ URL::previous() }}">
                            @endif
                        </div>
                        {{ csrf_field() }}
                        @if ($errors->has('email') || $errors->has('password') )
                            <div class="alert alert-danger alert-dismissible fade show">
                                <span> @lang('general.pages.login.error') </span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <span> {{ session('error') }} </span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('siverrors'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <span> {{ session('siverrors') }} </span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('api_failed'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <span> {{ session('api_failed') }} </span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="md-input-group">
                            <input type="text" id="email" name="email" placeholder="@lang('labels.email')"/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="email">@lang('labels.email')</label>
                        </div>
                        <div class="md-input-group">
                            <input type="password" id="password" name="password"
                                   placeholder="@lang('general.pages.login.password_label')"/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label for="password">@lang('general.pages.login.password_label')</label>
                        </div>
                        <div class="sign-form__footer d-flex justify-content-between align-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" id="rememberMe">
                                <label class="custom-control-label"
                                       for="rememberMe">@lang('general.pages.login.remember')</label>
                            </div>
                            <div class="sign-form__footer__forgot-pass">
                                <a class="primary-link" href="{{ route('password.request') }}">
                                    @lang('general.pages.login.recover_btn')
                                </a>
                            </div>
                        </div>
                        <div id="login_id"></div>
                        {!!  GoogleReCaptchaV3::renderOne('login_id', 'login_id') !!}
                        <div class="custom-recaptcha">
                            Acest website este protejat de reCAPTCHA v3 invisible și vizitatorilor li se aplică
                            <a target="_blank" href="https://policies.google.com/privacy"> Politica de
                                confidențialitate</a> și
                            <a target="_blank" href="https://policies.google.com/terms">Termenii de utilizare</a>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mx-auto d-block my-5">
                            @lang('general.pages.login.login_btn')
                        </button>
                        <div class="d-flex justify-content-end my-5">
                            <a class="primary-link" href="{{ route('register') }}">
                                @lang('general.pages.login.new_account')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-1">
            </div>
        </div>
    </div>
@endsection
