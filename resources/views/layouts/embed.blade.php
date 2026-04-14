<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="/organizz-icon.svg" type="image/svg+xml">
    @vite(['resources/js/public.js'])
</head>
<body class="min-h-screen bg-[#f4f6f9] font-sans text-slate-900 antialiased">
    @yield('content')
</body>
</html>
