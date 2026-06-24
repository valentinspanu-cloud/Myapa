<div class="js-cookie-consent cookie-consent">

    <span class="cookie-consent__message">
        {!! trans('cookieConsent::texts.message', ['link' => route('cms.view', getPage(7)['slug'])]) !!}
    </span>

    <button class="js-cookie-consent-agree cookie-consent__agree btn btn-primary">
        {{ trans('cookieConsent::texts.agree') }}
    </button>

</div>
