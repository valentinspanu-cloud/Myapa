@extends('layouts.app')
@section('title', trans('general.pages.invoice.thankYou_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.invoice.thankYou_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        {{ $message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
