@extends('layouts.login')
@section('title',trans('general.pages.403.title'))
@section('content')
        <div id="content-main">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <img src="{{asset('img/403.svg')}}" alt="403"
                             class="my-5 mx-auto d-block illustration-error"/>
                        <h2 class="mb-5 text-center">@lang('general.pages.403.no_permission')</h2>
                        <a class="btn btn-primary btn-lg d-table mx-auto" href="{{route('home')}}"
                           title="@lang('general.pages.403.back_to_home')">
                            @lang('general.pages.403.back_to_home')
                        </a>
                    </div>

                </div>
            </div>
        </div>
@endsection


