@extends('layouts.email')
@section('title', trans('general.emails.complaints.consumer-title'))
@section('preHeader', trans('general.emails.complaints.consumer-content_p1', ['id'=>$complaint->id]))
@section('content')
    <tr>
        <td class="wrapper"
            style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0"
                   style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            @lang('general.emails.general.dear_customer'),</p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            @if($complaint->status_id != 1)
                                {!! trans('general.emails.complaints.consumer-content_p3', ['id'=> '<b>' .$complaint->id . '</b>']) !!}
                            @else
                                {!! trans('general.emails.complaints.consumer-content_p1', ['id'=> '<b>' .$complaint->id . '</b>']) !!}
                            @endif
                        </p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">

                            @lang('general.emails.complaints.consumer-content_p2') <a
                                href="{{ route('complaints.show', $complaint->id) }}"
                                target="_blank"
                                style="color: #00B9FF; text-decoration: underline;">@lang('labels.your_account')</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
{{--@section('unsubscribe')
    <tr>
        <td class="content-block"
            style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #b3b3b3; text-align: center;">
            <br> @lang('general.emails.general.unsubscribe') <a href="{{ route('user.account') }}"
                                                                style="text-decoration: underline; color: #B3B3B3; font-size: 12px; text-align: center;">@lang('general.emails.general.your_member_account')</a>.
        </td>
    </tr>
@endsection--}}


