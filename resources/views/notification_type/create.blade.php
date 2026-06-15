@extends('layouts.app')
@section('title', trans('general.pages.notification_type.create_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.notification_type.create_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <form method="post" action="{{ route('notificationType.store') }}" class="form-sesizari">
                            {{csrf_field()}}
                            <div class="md-input-group">
                                <input type="text" id="subject" name="name" value="{{ old('name') }}" placeholder="@lang('labels.name')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="subject">@lang('labels.name')</label>
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>
                            <button type="submit"
                                    class="btn btn-primary d-table ml-auto mt-4">@lang('general.pages.notification_type.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
