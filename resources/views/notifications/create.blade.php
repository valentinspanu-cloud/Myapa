@extends('layouts.app')
@section('title', trans('general.pages.notifications.create_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.notifications.create_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <form method="post" action="{{ route('notification.store') }}" class="form-sesizari">
                            {{csrf_field()}}
                            <div class="row mt-4">
                                <div class="col-6 col-lg-12">
                                    <div class="md-input-group">
                                        <input type="text" id="date_from" name="date_from"/>
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label for="date_from">Data trimitere notificare</label>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-6" style="display: none;">
                                    <div class="md-input-group">
                                        <input type="text" id="date_to" name="date_to"/>
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label for="date_to">Valabilitate până la</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
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
                                        </div>

                                        <div class="col-md-12">
                                            <div class="md-input-group md-select-group">
                                                <select class="select-text" name="category" id="category">
                                                    <option value="all">Toate categoriile</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category }}">{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="select-highlight"></span>
                                                <span class="select-bar"></span>
                                                <label class="select-label"
                                                       for="category">@lang('labels.categories')</label>
                                                @if ($errors->has('category'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('category') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="md-input-group md-select-group">
                                                <select class="select-text select2-search" name="receiver_id" id="receiver_id">
                                                    <option value="">Toti clientii</option>
                                                    @foreach($clients as $client)
                                                        <option
                                                            value="{{ $client->id }}">{{ $client->codes[0]['client_code'] }}
                                                            -- {{ $client->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="select-highlight"></span>
                                                <span class="select-bar"></span>
                                                <label class="select-label"
                                                       for="receiver_id">@lang('labels.clients')</label>
                                                @if ($errors->has('receiver_id'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('receiver_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="md-input-group md-select-group">
                                                <select class="select-text" multiple name="sector[]" size="12" id="sector">
                                                    @foreach($sectors as $sector)
                                                        <option value="{{ $sector->sector_code }}--{{ $sector->city }} ">{{ $sector->sector_code }}
                                                            -- {{ $sector->city }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="select-highlight"></span>
                                                <span class="select-bar"></span>
                                                <label class="select-label" for="sector">@lang('labels.sectors')</label>
                                                @if ($errors->has('sector'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('sector') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="md-input-group">
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                       placeholder="@lang('labels.subject')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="subject">@lang('labels.subject')</label>
                                @if ($errors->has('subject'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('subject') }}
                                    </div>
                                @endif
                            </div>
                            <div class="md-input-group">
                                 <textarea id="sms_content" name="sms_content" class=""
                                           placeholder="@lang('labels.sms_content')">{{ old('sms_content') }}</textarea>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="content">@lang('labels.sms_content')</label>
                                @if ($errors->has('sms_content'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('sms_content') }}
                                    </div>
                                @endif
                            </div>
                            <label for="content">@lang('labels.email_content')</label>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="send_email" name="send_email" value="1" checked>
                                <label class="custom-control-label" for="send_email">Trimite și pe email</label>
                            </div>
                            <div class="md-input-group">
                                <textarea id="content" name="content" class="summernote"
                                          placeholder="@lang('labels.email_content')">{{ old('content') }}</textarea>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                @if ($errors->has('content'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('content') }}
                                    </div>
                                @endif
                            </div>
                            <button type="submit"
                                    class="btn btn-primary d-table ml-auto mt-4">@lang('general.pages.notifications.save')</button>
                        </form>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
jQuery(document).ready(function($) {
    $('#receiver_id').select2({
        placeholder: 'Caută după cod client sau nume...',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection
@section('header')
    {{--    <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection

@section('footer')
    {{--    <script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.ro.min.js') }}"></script>
    <script src="{{ asset('js/summernote-ro-RO.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 300,
                fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
                codeviewFilter: false,
                codeviewIframeFilter: true,
                airmode: false,
                lang: 'ro-RO',
                styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3'],
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear', 'style']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['link', ['linkDialogShow', 'unlink']],
                    /* ['styleTags', ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3']]*/
                ],

            });
        });
    </script>
@endsection

