<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-base-url" content="{{ config('api.base_url') }}">
    <title>@yield('title', config('app.name')) — EssenseLuxe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-stone-50 text-stone-900" x-data>
    <div class="min-h-screen flex flex-col">
        @include('components.navbar')

        <main class="flex-1">
            @include('components.flash-messages')
            @yield('content')
        </main>

        @include('components.footer')
    </div>
</body>
</html>
