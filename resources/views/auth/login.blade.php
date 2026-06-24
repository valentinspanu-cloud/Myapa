<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autentificare — Cititori Contoare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">

<div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea" class="h-16 mx-auto mb-4">
        <h1 class="text-xl font-bold text-[#1e3a5f]">Cititori Contoare</h1>
        <p class="text-sm text-gray-500 mt-1">Aquaserv Tulcea</p>
    </div>

    {{-- Erori --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm mb-6">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Parolă</label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent pr-12">
                <button type="button" onclick="togglePass()"
                    class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>
        <script>
        function togglePass() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
        </script>

        <div class="flex items-center">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded accent-[#1e3a5f]">
                Ține-mă minte
            </label>
        </div>

        <button type="submit" id="login-btn"
            class="w-full bg-[#1e3a5f] hover:bg-[#2d5a8e] text-white font-semibold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
            <span id="login-btn-text">Autentificare</span>
        </button>
    </form>

    <script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        const text = document.getElementById('login-btn-text');
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        text.innerHTML = `
            <svg class="animate-spin h-5 w-5 inline-block mr-2" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Se autentifică...
        `;

        // Afisam overlay fullscreen cu mic delay, ca sa nu clipeasca pe autentificari rapide
        setTimeout(function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }, 400);
    });
    </script>

    <div class="text-center mt-6 text-xs text-gray-400">
        © {{ date('Y') }} Aquaserv Tulcea
    </div>
</div>

</body>
</html>
