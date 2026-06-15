@extends('layouts.' . (Auth::check() ? 'app' : 'login'))
@section('title', $page->title)
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    
		    @if($page->id==4 || $page->id==10 || $page->id==11 || $page->id==12)
                        <ul class="nav-page-info">

			    @if(getPage(4))
                                  <li class="nav-item">
                                     <a class="btn btn-primary" href="{{ route('cms.view', getPage(4)['slug']) }}">{{ getPage(4)['title'] }}</a>
                                 </li>
                              @endif
 <li class="nav-item">
                                     <a class="btn btn-primary" href="{{ route('cms.view', getPage(2)['slug']) }}">{{ getPage(2)['title'] }}</a>
                                 </li>                              
			    @if(getPage(10))
                                  <li class="nav-item">
                                     <a class="btn btn-primary" href="{{ route('cms.view', getPage(10)['slug']) }}">{{ getPage(10)['title'] }}</a>
                                 </li>
                              @endif
                                @if(getPage(11))
                                    <li class="nav-item">
                                        <a class="btn btn-primary" href="{{ route('cms.view', getPage(11)['slug']) }}">{{ getPage(11)['title'] }}</a>
                                    </li>
                                @endif
                                @if(getPage(12))
                                    <li class="nav-item">
                                        <a class="btn btn-primary" href="{{ route('cms.view', getPage(12)['slug']) }}">{{ getPage(12)['title'] }}</a>
                                    </li>
                                @endif
                        </ul>
                    @endif
<h1>{{ $page->title }}</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        {!!  $page->content  !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


