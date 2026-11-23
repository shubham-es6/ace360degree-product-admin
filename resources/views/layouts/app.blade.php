<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- sidebarOpen only matters on small screens, where the sidebar slides in. --}}
<body class="min-h-screen bg-page font-sans text-ink antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">

    {{-- Dark overlay behind the open mobile sidebar; clicking it closes the sidebar. --}}
    <div x-cloak x-show="sidebarOpen" x-transition.opacity
         class="fixed inset-0 z-40 bg-ink/40 lg:hidden"
         @click="sidebarOpen = false"></div>

    @include('partials.sidebar')

    {{-- Everything right of the 256px sidebar (desktop) / full width (mobile). --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        @include('partials.header')

        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
