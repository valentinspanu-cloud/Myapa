@extends('layouts.app')
@section('title', 'Notificări')
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.notifications.title_consumer')</h1>
                </div>
            </div>
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
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <div class="table-responsive">
                            <table id="notif-table" class="table table-actions custom-table"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.type')</th>
                                    <th>@lang('labels.subject')</th>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.actions')
                                        <form method="post" class="delete-all"
                                              action="{{ route('notification.destroyAll') }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="ids" id="not-ids" value=""/>
                                            <button title="Sterge notificarile selectate"
                                                    class="hover-to-accent">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notifications as $notification)
                                    <tr class="{{ !in_array($notification->id, $seenNotifications) ? 'unread' : '' }}">
                                        <td><input type="checkbox" value="{{ $notification->id }}"
                                                   name="notifications[]" class="hidden-id"/></td>
                                        <td>{{ $notification->type->name }}</td>
                                        <td>{{ $notification->subject }}</td>
                                        <td>{{ $notification->created_at }}</td>
                                        <td>
                                            @if(in_array($notification->id, $seenNotifications))
                                                @lang('labels.read')
                                            @else
                                                @lang('labels.unread')
                                            @endif
                                        </td>
                                        <td>
                                            <a class="hover-to-accent"
                                               href="{{ route('notification.view', $notification->id) }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form method="post"
                                                  action="{{ route('notification.destroy', $notification->id) }}">
                                                {{ csrf_field() }}
                                                <button type="submit" title="Sterge notificarea"
                                                        class="hover-to-accent delete-icon">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
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
