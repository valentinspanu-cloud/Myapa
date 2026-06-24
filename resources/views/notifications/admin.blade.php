@extends('layouts.app')
@section('title', trans('general.pages.notifications.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.notifications.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <div class="buttons-container">
                            @if(auth()->user()->hasRole('notifications_manager'))
                                <a href="{{ route('notification.create') }}"
                                   class="btn btn-primary notification_create">
                                    @lang('general.pages.notifications.new_btn')
                                </a>
                            @endif
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('notificationType.list') }}" class="btn btn-info notification_type">
                                    @lang('general.pages.notification_type.title')
                                </a>
                            @endif
                        </div>
                        <div class="clearfix custom_clearfix"></div>
                        <hr class="add-new-hr"/>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="box-container__title__w-filter">
                            <div class="filter-form">
                                <label for="start-date">Data crearii intre</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="from"
                                       id="start-date"/>
                                <label for="end-date">si</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="to"
                                       id="end-date"/>
                                <input type="submit" value="Filtrează"
                                       class="btn btn-outline-secondary btn-filter"/>
                                <input type="submit" value="Resetează" class="btn btn-link btn-reset"/>
                            </div>
                        </div>

                        <table id="notifications-table" class="table table-actions" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('labels.id')</th>
                                <th>@lang('labels.type')</th>
                                <th>@lang('labels.user')</th>
                                <th>@lang('labels.subject')</th>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.status')</th>
                                <th>@lang('labels.actions')</th>
                            </tr>
                            </thead>
                        </table>
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
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}"></script>
@endsection
