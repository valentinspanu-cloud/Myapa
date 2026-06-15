@extends('layouts.app')
@section('title', trans('general.pages.cms.edit_title'))

@section('content')
  @if(!auth()->user()->hasRole('admin'))
     @if((auth()->user()->hasRole('closingwater_manager') && !in_array($page->id, [10,11])) || (auth()->user()->hasRole('bulletinanalysis_manager') && $page->id !=12))
        @php
            header("Location: " . URL::to('/'), true, 302);
            exit();
        @endphp
     @endif
  @endif
 <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.cms.edit_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <form method="post" class="form-w-inputs-margins" action="{{ route('cms.update',['cms' => $page->id]) }}">
                            <input type="hidden" name="_method" value="PUT"/>
                            {{ csrf_field() }}
                            <div class="md-input-group">
                                <input type="text" id="title" name="title" autocomplete="false" value="{{ $page->title }}"
                                       placeholder="@lang('labels.title')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="title">@lang('labels.title')</label>
                                @if ($errors->has('title'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('title') }}
                                    </div>
                                @endif
                            </div>

                            <div class="md-input-group">
                                <input type="text" id="slug" name="slug" value="{{ $page->slug }}"
                                       placeholder="@lang('labels.slug')"/>
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label for="slug">@lang('labels.slug')</label>
                                @if ($errors->has('slug'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('slug') }}
                                    </div>
                                @endif
                            </div>

                            <div class="md-input-group md-select-group">
                                <select class="select-text" name="status" id="status" required>
                                    <option value="" disabled selected></option>
                                    <option {{ $page->status == 'Activ' ? 'selected="selected"' : '' }} value="Activ">
                                        Activ
                                    </option>
                                    <option {{ $page->status == 'Inactiv' ? 'selected="selected"' : '' }} value="Inactiv">
                                        Inactiv
                                    </option>
                                </select>
                                <span class="select-highlight"></span>
                                <span class="select-bar"></span>
                                <label class="select-label" for="status">@lang('labels.status')</label>
                            </div>

                            <textarea name="content" class="summernote" placeholder="@lang('labels.content')">
                                {{ $page->content }}
                            </textarea>
                            @if ($errors->has('content'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('content') }}
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary d-table ml-auto mt-3">@lang('labels.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('header')
{{--    <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">
@endsection

@section('footer')
{{--    <script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>
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
                    ['table', ['table']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                   /* ['styleTags', ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3']]*/
                ],

            });
        });
    </script>
@endsection

