@if(isset($actionUrl))
    @if(str_contains($actionUrl, 'password/reset') || str_contains($actionUrl, 'password-reset'))
        @include('emails.recover', ['token' => basename(parse_url($actionUrl, PHP_URL_PATH))])
    @else
        @include('emails.verify', ['url' => $actionUrl])
    @endif
@else
    <span>Something went wrong, please try again</span>
@endif
