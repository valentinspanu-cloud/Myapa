@extends('layouts.app')
@section('title', trans('general.pages.complaints.edit_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.complaints.popup_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-md-6">
                    <div class="bg-white shadow-sm box-container h-100">
                        <p><strong>@lang('labels.nr') </strong>{{ $complaint['id'] }}</p>
                        <p>
                            <strong>@lang('labels.consumer'): </strong>
                            {{ $complaint['reporter']['codes'][0]['client_code'] }}
                        </p>
                        <p><strong>@lang('labels.location'): </strong>{{ $complaint['location'] }}</p>
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
                        @if($complaint['answer'])
                            <p>
                                <strong>@lang('labels.answer'): </strong>
                                <br/>
                                {{ $complaint['answer'] }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="bg-white shadow-sm box-container h-100">
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
