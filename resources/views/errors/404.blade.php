@extends('layouts.login')
@section('title',trans('general.pages.404.title'))
@section('content')
        <div id="content-main" class="page-404">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <img src="{{asset('img/page_not_found.svg')}}" alt="404"
                             class="my-5 mx-auto d-block illustration-error"/>
                        <h1 class="mb-5 text-center">@lang('general.pages.404.title')</h1>
                        <a class="btn btn-primary btn-lg d-table mx-auto" href="{{route('home')}}"
                           title="@lang('general.pages.404.back_to_home')">
                            @lang('general.pages.404.back_to_home')
                        </a>
                    </div>

                </div>
            </div>
        </div>
 @endsection


