@extends('layouts.app')
@section('title', trans('general.pages.complaint_type.edit_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.complaint_type.edit_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <form method="post" action="{{ route('complaintType.update', $type->id) }}"
                              class="form-sesizari">
                            <input type="hidden" name="_method" value="PUT"/>
                            {{csrf_field()}}
                            <div class="md-input-group">
                                <input type="text" id="subject" name="name" value="{{ $type->name }}"
                                       placeholder="@lang('labels.name')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="subject">@lang('labels.name')</label>
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>
                            <div style="margin-top: 20px;">
                                <label>Responsabili: </label>
                                <select class="select-text" name="user_id[]" id="userID" multiple>
                                    @foreach($users as $user)
                                        <option
                                            {{ !empty($complaintUsers[$user->id]) ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('user_id'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('user_id') }}
                                    </div>
                                @endif
                            </div>
                            <button type="submit"
                                    class="btn btn-primary d-table ml-auto mt-4">@lang('general.pages.complaint_type.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
