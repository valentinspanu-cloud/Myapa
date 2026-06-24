@extends('layouts.app')
@section('title', trans('general.pages.cms.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.cms.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <table class="table table-actions" id="default-table" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('labels.title')</th>
                                <th>@lang('labels.slug')</th>
                                <th>@lang('labels.status')</th>
                                <th>@lang('labels.actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $canSee = false; ?>
                            @foreach($pages as $page)
                                @if(auth()->user()->hasRole('closingwater_manager') && in_array($page->id, [10,11]))
                                    <?php $canSee = true; ?>
                                @elseif(auth()->user()->hasRole('bulletinanalysis_manager') && ($page->id ==12))
                                    <?php $canSee = true; ?>
                                @elseif(auth()->user()->hasRole('admin') && !in_array($page->id, [10,11]))
                                    <?php $canSee = true; ?>
                                @else
                                    <?php $canSee = false; ?>
                                @endif
                                @if($canSee)
                                    <tr>
                                        <td>{{ $page->title }}</td>
                                        <td>{{ $page->slug }}</td>
                                        <td>{{ $page->status }}</td>
                                        <td>
                                            <a class="hover-to-accent" href="{{ route('cms.edit', $page->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach 
			    </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
