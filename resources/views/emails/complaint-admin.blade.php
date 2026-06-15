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
                            @lang('general.emails.general.dear_admin'),</p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                        {!! trans('general.emails.complaints.admin-content_p1', ['id'=> '<b>' .$complaint->id . '</b>']) !!}
                        </p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            {!! trans('general.emails.complaints.admin-content_p2', ['button'=> '<a href="'.route('complaints.edit', $complaint->id).'"
                                                                                      target="_blank" style="color: #00B9FF; text-decoration: underline;">'.trans('labels.click_here').'</a>']) !!}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
