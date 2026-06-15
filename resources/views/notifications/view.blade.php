@extends('layouts.app')
@section('title', $notification->subject)
@section('content')
    <div id="content-main" class="notification">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <h1>
                            <span class="font-weight-light">@lang('labels.subject'): </span>{{ $notification->subject }}
                        </h1>

                    </div>
                    @if(auth()->user()->hasRole(['admin', 'notifications_manager']))
                        <p>
                            @lang('labels.created_by'):
                            <strong>
                                {{  $notification->user->name }}
                            </strong><br/>

                            @lang('labels.date'):
                            <strong>
                                {{  $notification->created_at }}
                            </strong><br/>

                            @lang('labels.status'):
                            <strong>{{  $notification->status->name }}</strong><br/>
                            @if($notification->date_from)
                            Data trimiterii: {{--@lang('labels.date_from')--}}

                                <strong>{{  $notification->date_from->format('Y/m/d') }}</strong> {{--@lang('labels.date_to')--}}
                                {{--:
                                <strong>{{  $notification->date_to->format('Y/m/d') }}</strong>--}}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'notifications_manager']))
                <div class="row mt-4">
                    <div class="col-12 col-lg-12">
                        <div class="bg-white shadow-sm box-container">
                            <h5>@lang('labels.sms_content')</h5>
                            <hr/>
                            {!! $notification->sms_content !!}
                        </div>
                    </div>
                </div>
            @endif
            <div class="row mt-4">
                <div class="col-12 col-lg-12">
                    <div class="bg-white shadow-sm box-container">
                        <h5>@lang('labels.content')</h5>
                        <hr/>
 			<p>@lang('general.emails.general.dear_customer')</p>
                        {!! $notification->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
