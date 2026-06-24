@extends('layouts.app')
@section('title', 'Administrare utilizatori')
@section('content')

    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.users.create_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-lg-12">
                    <div class="bg-white shadow-sm box-container">
                        <form method="post" class="custom-form--input-spacing" action="{{ route('users.store') }}">
                            {{ csrf_field() }}
                            <div class="md-input-group">
                                <input type="text" name="name" id="name" value="" placeholder="@lang('labels.name')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="name">@lang('labels.name')</label>
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>

                            <div class="md-input-group">
                                <input type="text" name="email" id="email" value=""
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
                            {{--<div class="md-input-group d-none">
                                <input type="text" name="phone" id="phone" value="00000000000"
                                       placeholder="@lang('labels.phone')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="email">@lang('labels.phone')</label>
                                @if ($errors->has('phone'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('phone') }}
                                    </div>
                                @endif
                            </div>--}}
                            <div class="md-input-group">
                                <input type="password" name="password" id="password" value=""
                                       placeholder="@lang('labels.password')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="password">@lang('labels.password')</label>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback d-block">
                                        {!! $errors->first('password') !!}
                                    </div>
                                @endif
                            </div>
                            <div class="md-input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" value=""
                                       placeholder="@lang('labels.password_confirmation')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="password_confirmation">@lang('labels.password_confirmation')</label>
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>
                            <div class="md-input-group md-select-group">
                                <select class="select-text" name="status" id="status" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                                    @endforeach
                                </select>
                                <span class="select-highlight"></span>
                                <span class="select-bar"></span>
                                <label class="select-label" for="status">@lang('labels.status')</label>
                            </div>

                            <div class="form-group">
                                <label for="role">@lang('labels.role')</label>
                                <select class="form-control" multiple name="role[]" id="role" required>
                                    @foreach($roles as  $role)
                                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary d-table ml-auto my-3">@lang('labels.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
