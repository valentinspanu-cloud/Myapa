@extends('layouts.app')
@section('title', 'Administrare utilizatori')
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.users.title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <a href="{{ route('users.create') }}" class="btn btn-primary d-table">
                            @lang('general.pages.users.new_user_btn')
                        </a>
                        <hr class="add-new-hr"/>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="@lang('labels.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="usersTable" class="table" style="width:100%">
                                <thead>
                                <tr>
                                    <th>@lang('labels.id')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.email')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.role')</th>
                                    <th>@lang('labels.created_at')</th>
                                    <th>@lang('labels.updated_at')</th>
                                    <th>@lang('labels.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->statusdata['name'] }}</td>
                                        <td>{{ implode(',', \Spatie\Permission\Models\Role::whereIn('name', $user->getRoleNames())->get()->pluck('display_name')->toArray()) }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>{{ $user->updated_at }}</td>
                                        <td>
                                            <a href="{{ route('users.edit', $user['id']) }}"
                                               title="@lang('labels.edit')"
                                               class="hover-to-accent">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">@lang('general.pages.users.no_users')</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <h1>@lang('general.pages.users.clients_title')</h1>
                        <hr class="add-new-hr"/>
                        <div class="table-responsive">
                            <table id="clientsTable" class="table" style="width:100%">
                                <thead>
                                <tr>
                                    <th>@lang('labels.nr')</th>
                                    <th>@lang('labels.client_code')</th>
                                    <th>@lang('labels.client_id')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.email')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-confirm-modal" tabindex="-1" role="dialog"
         aria-labelledby="delete-confirm-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="delete-confirm-modal-label">@lang('general.pages.users.delete_confirm_title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        @lang('general.pages.users.delete_confirm_body_1')
                        <strong class="user-name">

                        </strong>
                        @lang('general.pages.users.delete_confirm_body_2')
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark"
                            data-dismiss="modal">@lang('labels.no_cancel')</button>
                    <button type="button" class="btn btn-danger delete-submit"
                            data-todelete="0">@lang('general.pages.users.delete_confirm_submit')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
