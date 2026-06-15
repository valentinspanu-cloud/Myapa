<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/redesign.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <script>window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?></script>
</head>
<body>
@yield('content')
<script src="{{ asset('js/app.js') }}"></script>
{!! GoogleReCaptchaV3::init() !!}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-pass').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.getElementById(this.getAttribute('data-target'));
            if (!input) return;
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if(icon) { icon.classList.replace('fa-eye', 'fa-eye-slash'); }
            } else {
                input.type = 'password';
                if(icon) { icon.classList.replace('fa-eye-slash', 'fa-eye'); }
            }
        });
    });
});
</script>
</body>
</html>
