<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Hosting Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
    <div id="sidebar-backdrop" data-sidebar-close
        class="fixed inset-0 z-30 hidden bg-gray-900/50 lg:hidden"></div>

    <aside id="admin-sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-60 -translate-x-full flex-col bg-gray-900 px-4 py-6 transition-transform duration-200 ease-out lg:translate-x-0">
        <div class="mb-8 flex items-center justify-between gap-2 px-2">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-500 font-bold text-white">H</span>
                <span class="truncate text-white font-semibold">Hosting Tracker</span>
            </a>
            <button type="button" data-sidebar-close aria-label="Close menu"
                class="shrink-0 rounded-lg p-1.5 text-gray-400 hover:bg-white/5 hover:text-white lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                <span>▦</span> Dashboard
            </a>
            <a href="{{ route('admin.services.index') }}"
                class="nav-link {{ request()->routeIs('admin.services.*') ? 'nav-link-active' : '' }}">
                <span>◈</span> Services
            </a>
            <a href="{{ route('admin.providers.index') }}"
                class="nav-link {{ request()->routeIs('admin.providers.*') ? 'nav-link-active' : '' }}">
                <span>⌂</span> Providers
            </a>
            <a href="{{ route('admin.provider-accounts.index') }}"
                class="nav-link {{ request()->routeIs('admin.provider-accounts.*') ? 'nav-link-active' : '' }}">
                <span>⚿</span> Provider Accounts
            </a>
            <a href="{{ route('admin.clients.index') }}"
                class="nav-link {{ request()->routeIs('admin.clients.*') ? 'nav-link-active' : '' }}">
                <span>☺</span> Clients
            </a>
        </nav>

        <div class="border-t border-white/10 pt-4">
            <p class="truncate px-3 text-xs text-gray-400">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="nav-link w-full">
                    <span>⎋</span> Log out
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-60">
        <header
            class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" id="sidebar-toggle" aria-controls="admin-sidebar" aria-expanded="false"
                    aria-label="Open menu"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1 class="truncate text-base font-semibold sm:text-lg">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                @hasSection('header-actions')
                    @yield('header-actions')
                @endif
            </div>
        </header>

        <main class="space-y-6 p-4 sm:p-6">
            @if (session('status'))
                <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>
