<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acces interzis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="text-center bg-white rounded-2xl shadow-sm p-8 max-w-sm w-full">
        <div class="text-5xl mb-4">🔒</div>
        <h1 class="text-xl font-bold text-[#1e3a5f] mb-2">Acces restricționat</h1>
        <p class="text-gray-600 text-sm mb-6">
            {{ $exception->getMessage() ?: 'Nu aveți permisiunea de a accesa această pagină.' }}
        </p>
        <a href="/cititor" class="inline-block bg-[#1e3a5f] text-white px-6 py-2.5 rounded-xl text-sm font-medium">
            ← Înapoi la lista de citiri
        </a>
    </div>
</body>
</html>
