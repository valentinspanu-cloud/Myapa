@extends('layouts.app')
@section('title', trans('general.pages.complaints.edit_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.complaints.edit_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-md-6">
                    <div class="bg-white shadow-sm box-container h-100">
                        <p><strong>@lang('labels.nr'): </strong>{{ $complaint['id'] }}</p>
                        <p>
                            <strong>@lang('labels.consumer'): </strong>
                            {{ $complaint['reporter']['codes'][0]['client_code'] }}
                        </p>
                        <p><strong>@lang('labels.location'): </strong>{{ $complaint['location'] }}</p>
                        <p><strong>@lang('labels.type'): </strong>{{ $complaint['type']['name'] }}</p>
                        <p><strong>@lang('labels.status'): </strong>{{ $complaint['status']['name'] }}</p>
                        <p><strong>@lang('labels.created_at')
                                : </strong>{{ date('d.m.Y', strtotime($complaint['created_at'])) }}</p>
                                            </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="bg-white shadow-sm box-container h-100">
                        <p><strong>@lang('labels.subject'): </strong>{{ $complaint['subject'] }}</p>
                        <p>
                            <strong>@lang('labels.description'): </strong>
                            <br/>
                            {{ $complaint['description'] }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-4">
                    <div class="bg-white shadow-sm box-container">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span>{{ session('error') }}</span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="update-complaint">
                            <form method="post"
                                  action="{{ route('complaints.update', $complaint['id']) }}"
                                  class="update-complaint-form">
                                {{csrf_field()}}
                                <input type="hidden" name="_method" value="put"/>
                                <div class="md-input-group mt-3">
                                                    <textarea name="comment" id="comment" rows="5"
                                                              placeholder="Comentariu">{{ $complaint['answer'] }}</textarea>
                                    <span class="highlight"></span>
                                    <span class="bar"></span>
                                    <label for="answer">Comentariu</label>
                                    @if ($errors->has('comment'))
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('comment') }}
                                        </div>
                                    @endif
                                </div>
                                <div style="margin-top: 20px;">
                                    <label>Status</label>
                                    <select class="select-text" name="status_id" id="statusComplaint">
                                        @foreach($statuses as $status)
                                            <option
                                                {{ $status->id == $complaint->status_id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="margin-top: 20px;">
                                    <label>Tip sesizare</label>
                                    <select class="select-text" name="type_id" id="typeComplaint">
                                        @foreach($types as $type)
                                            <option
                                                {{ $type->id == $complaint->type_id ? 'selected' : '' }} value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="btn btn-primary d-table ml-auto mt-4">@lang('labels.save')</button>
                                </div>
                            </form>
                        </div>
                        <h2>Istoric sesizare</h2>
                        <table id="commentsAdminTable" class="table table-actions" style="width:100%">
                            <thead>
                            <tr>
                                <th>Responsabil</th>
                                <th>Status</th>
                                <th>Tip</th>
                                <th>Comentariu</th>
                                <th>Data</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($complaint->comments as $comment)
                                <tr>
                                    <td>{{ $comment->user->name }}</td>
                                    <td>{{ $comment->status->name }}</td>
                                    <td>{{ $comment->type->name }}</td>
                                    <td>{{ $comment->comment }}</td>
                                    <td>{{ $comment->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


