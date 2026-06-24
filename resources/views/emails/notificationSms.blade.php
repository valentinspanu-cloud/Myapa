@extends('layouts.sms')
@section('title', $notification->subject)
@section('preHeader', $notification->subject)
@section('content')
                        {!! $notification->sms_content  !!}
@endsection
