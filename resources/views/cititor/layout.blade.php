<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a5f">
    <title>@yield('title', 'Cititor Contoare') — AquaServ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a5f',
                        'primary-light': '#2d5a8e',
                    }
                }
            }
        }
    </script>
    <style>
        /* Previne zoom la focus pe input pe iOS */
        input, select, textarea { font-size: 16px !important; }
        .status-nou      { @apply bg-blue-100 text-blue-800; }
        .status-confirmat{ @apply bg-green-100 text-green-800; }
        .status-eroare   { @apply bg-red-100 text-red-800; }
        .status-corectat { @apply bg-yellow-100 text-yellow-800; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Header mobil --}}
    <header class="bg-[#1e3a5f] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-50 shadow-md">
        <div class="flex items-center gap-3">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V9l-6-6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v6h6"/>
            </svg>
            <div>
                <div class="font-semibold text-sm leading-tight">@yield('header_title', 'Cititor Contoare')</div>
                <div class="text-xs text-blue-200 flex items-center gap-2">
                    <span>{{ Auth::user()->name }}</span>
                    @hasSection('luna_header')
                        @yield('luna_header')
                    @endif
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-blue-200 hover:text-white text-xs underline">
                Ieșire
            </button>
        </form>
    </header>

    {{-- Mesaje flash --}}
    <div class="px-4 pt-3 space-y-2">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                </svg>
                {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Search sticky (optional per pagina) --}}
    @yield('search')

    {{-- Continut principal --}}
    <main class="px-4 py-4 pb-24">
        @yield('content')
    </main>

@stack('scripts')
</body>
</html>
