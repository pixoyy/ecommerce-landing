<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-base-url" content="{{ config('api.base_url') }}">
    <title>@yield('title', config('app.name')) — EssenseLuxe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-stone-50 text-stone-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-stone-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="/" class="text-xl font-bold tracking-tight text-stone-900">
                        EssenseLuxe
                    </a>
                    <nav class="flex items-center gap-4">
                        <a href="/login" class="text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">
                            Masuk
                        </a>
                        <a href="/register"
                           class="text-sm font-medium bg-stone-900 text-white px-4 py-2 rounded-lg hover:bg-stone-800 transition-colors">
                            Daftar
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 py-12">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-stone-200 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-stone-500">
                &copy; {{ date('Y') }} EssenseLuxe. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
