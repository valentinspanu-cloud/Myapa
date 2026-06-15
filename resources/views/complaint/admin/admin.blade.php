@extends('layouts.app')
@section('title', 'Administrare  sesizări')
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.complaints.admin_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('complaintStatus.list') }}" class="btn btn-primary">
                                @lang('general.pages.complaint_status.title')
                            </a>
                            <a href="{{ route('complaintType.list') }}" class="btn btn-info">
                                @lang('general.pages.complaint_type.title')
                            </a>
                        @endif
                        <div class="clearfix custom_clearfix"></div>
                        <hr class="add-new-hr"/>
                        <div class="row">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-label="@lang('labels.close')">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span>{{ session('error') }}</span>
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-label="@lang('labels.close')">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <div class="box-container__title__w-filter">
                                <div class="filter-form">
                                    <label for="start-date">Data raportarii intre</label>
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

                            <table id="complaintsAdminTable" class="table table-actions" style="width:100%">
                                <thead>
                                <tr>
                                    <th>@lang('labels.nr')</th>
                                    <th>@lang('labels.type')</th>
                                    <th>@lang('labels.consumer')</th>
                                    <th>@lang('labels.location')</th>
                                    <th>@lang('labels.report_date')</th>
                                    <th>@lang('labels.subject')</th>
                                    <th>@lang('labels.status')</th>
                                    <th class="text-center">@lang('labels.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}"></script>
@endsection
