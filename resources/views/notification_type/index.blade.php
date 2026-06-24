@extends('layouts.app')
@section('title', 'Notificări')
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.notification_type.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <a href="{{ route('notificationType.create') }}" class="btn btn-primary d-table">
                            @lang('general.pages.notification_type.new_btn')
                        </a>
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
                        <div class="table-responsive">
                            <table id="default-table" class="table table-actions" style="width:100%">
                                <thead>
                                <tr>
                                    <th>@lang('labels.id')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notificationTypes as $notification)
                                    <tr>
                                        <td>{{ $notification->id }}</td>
                                        <td>{{ $notification->name }}</td>
                                        <td>
                                            <a class="hover-to-accent"
                                               href="{{ route('notificationType.edit', $notification->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
