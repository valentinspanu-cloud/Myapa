@extends('layouts.app')
@section('title', trans('general.pages.complaints.title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.complaints.title')</h1>
                </div>
            </div>
            @if (session('success_delete'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible fade show">
                            <span>{{ session('success_delete') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="@lang('labels.close')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                    </div>
                </div>
            @endif
            <div class="row mt-4">
                @if(empty($locations))
                    <div class="col-12 col-lg-12">
                        <div class="bg-white shadow-sm box-container">
                            <p>@lang('general.pages.index.no_locations')</p>
                        </div>
                    </div>
                @else
                    <div class="col-12 col-lg-6">
                        <div class="bg-white shadow-sm box-container">
                            <form method="post" action="{{ route('complaints') }}">
                                {{ csrf_field() }}
                                <label for="locatie-consum"
                                       class="box-container__title">@lang('labels.locations')</label>
                                <select class="custom-select change-location" name="location" id="locatie-consum">
                                    @foreach($locations as $location)
                                        <option
                                            {{ $location['cod_loc'] == $currentLocation['cod_loc'] ? 'selected' : '' }}
                                            value="{{ $location['cod_loc'] }}">{{ $location['addr_text'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="bg-white shadow-sm box-container">
                            <h4 class="box-container__title">@lang('general.pages.complaints.new_title')</h4>
                            <form class="form-sesizari" method="post" action="{{ route('complaints.store') }}">
                                {{ csrf_field() }}
                                @if (session('success'))
                                    <div class="valid-feedback d-block">
                                        <button class="close" data-close="alert"></button>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="md-input-group md-select-group">
                                    <select class="select-text" name="type_id" id="type_id">
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="select-highlight"></span>
                                    <span class="select-bar"></span>
                                    <label class="select-label" for="type_id">@lang('labels.type')</label>
                                    @if ($errors->has('type_id'))
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('type_id') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="md-input-group">
                                    <input type="text" id="subiect" name="subject" value="{{ old('subject') }}"
                                           placeholder="@lang('labels.subject')"/>
                                    <span class="highlight"></span>
                                    <span class="bar"></span>
                                    <label for="subiect">@lang('labels.subject')</label>
                                    @if ($errors->has('subject'))
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('subject') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="md-input-group">
                                    <textarea id="mesaj" name="description"
                                              placeholder="@lang('labels.description')">{{ old('description') }}</textarea>
                                    <span class="highlight"></span>
                                    <span class="bar"></span>
                                    <label for="mesaj">@lang('labels.description')</label>
                                    @if ($errors->has('description'))
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('description') }}
                                        </div>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary d-table ml-auto mt-4">
                                    @lang('general.pages.complaints.send_btn')
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <div class="box-container__title__w-filter">
                            <h4>@lang('general.pages.complaints.history')</h4>
                        </div>
                        <div class="box-container__title__w-filter">
                            <div class="filter-form">
                                <label for="start-date">Data raportarii intre</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="from"
                                       id="start-date"/>
                                <label for="end-date">si</label>
                                <input type="text" data-date-end-date="0d" class="form-control" name="to"
                                       id="end-date"/>
                                <input type="submit" value="Filtrează"
                                       class="btn btn-outline-secondary btn-filter"/>
                                <input type="submit" value="Resetează" class="btn btn-link btn-reset"/>
                            </div>
                        </div>
                        <table class="table table-actions" id="userComplaintsTable" style="width:100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>@lang('labels.nr')</th>
                                <th>@lang('labels.report_date')</th>
                                <th>@lang('labels.location')</th>
                                <th>@lang('labels.subject')</th>
                                <th>@lang('labels.status')</th>
                                <th>@lang('labels.type')</th>
                                <th style="position:relative;">
                                    @lang('labels.actions')
                                    <form method="post" class="delete-all"
                                          action="{{ route('complaints.destroyAll') }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="ids" id="not-ids" value=""/>
                                        <input type="hidden" name="_method" value="delete"/>
                                        <button title="Sterge notificarile selectate" class="hover-to-accent">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($complaints as $complaint)
                                <tr>
                                    <td>
                                        @if($complaint->status_id == 3)
                                            <label>
                                                <input type="checkbox" value="{{ $complaint->id }}"
                                                       name="complaints[]" class="hidden-id"/>
                                            </label>
                                        @endif
                                    </td>
                                    <td style="width: 50px !important;">{{ $complaint->id }}</td>
                                    <td>{{ $complaint->created_at }}</td>
                                    <td>{{ $complaint->location }}</td>
                                    <td>{{ $complaint->subject }}</td>
                                    <td>{{ $complaint->status->name }}</td>
                                    <td>{{ $complaint->type['name'] }}</td>
                                    <td>
                                        <a href="{{ route('complaints.show', $complaint['id']) }}"
                                           title="@lang('labels.view')"
                                           class="hover-to-accent">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                        @if($complaint->status_id == 1)
                                            <form action="{{ route('complaints.delete', $complaint->id) }}"
                                                  class="d-inline-block"
                                                  method="POST" name="delete-{{$complaint->id}}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE"/>
                                                <button type="submit" style="border: 0; background: none;">
                                                    <i title="Sterge" class="fa fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">@lang('general.pages.complaints.no_complaints')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('header')
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('footer')
    <script src="{{ asset('js/range_dates.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}"></script>
@endsection
