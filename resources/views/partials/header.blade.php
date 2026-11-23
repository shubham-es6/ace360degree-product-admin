{{--
    Top bar shared by every page.
    Pages fill it in with:
      @section('breadcrumb', 'Products')   – small grey text before the "/"
      @section('title', 'Product List')    – page heading (also used in <title>)
      @section('header-actions') ... @endsection – buttons on the right
--}}
<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-line bg-white px-4 shadow-sm sm:px-6">

    <div class="flex min-w-0 flex-1 items-center gap-4 lg:gap-6">
        {{-- Hamburger, mobile only --}}
        <button type="button" class="-ml-1 rounded-lg p-2 text-muted hover:bg-page lg:hidden"
                @click="sidebarOpen = true" aria-label="Open menu" aria-controls="sidebar">
            <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                <path d="M3 5h14M3 10h14M3 15h14" />
            </svg>
        </button>

        {{-- Breadcrumbs --}}
        <div class="flex shrink-0 items-center gap-2">
            @hasSection('breadcrumb')
                <span class="hidden text-sm leading-5 font-medium tracking-[0.07px] whitespace-nowrap text-muted sm:inline">@yield('breadcrumb')</span>
                <span class="hidden text-base leading-6 text-line sm:inline" aria-hidden="true">/</span>
            @endif
            <h1 class="text-base leading-[22px] font-semibold tracking-[-0.16px] whitespace-nowrap text-ink">@yield('title', 'Dashboard')</h1>
        </div>

        {{-- Global search (desktop only): sends you to the product list with ?search=... --}}
        <form method="GET" action="{{ route('products.index') }}" role="search" class="relative hidden w-[276px] min-w-0 shrink lg:block">
            <label for="global-search" class="sr-only">Search products or SKU</label>
            <img src="{{ asset('images/icons/search.svg') }}" alt="" width="13.5" height="13.5"
                 class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2">
            <input id="global-search" type="search" name="search" value="{{ request('search') }}"
                   placeholder="Search products or SKU..."
                   class="h-9 w-full rounded-lg border border-line bg-white pr-3 pl-[37px] text-[13px] text-ink placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
        </form>
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        @yield('header-actions')

        <span class="mx-1 hidden h-5 w-px bg-line sm:block" aria-hidden="true"></span>

        {{-- Placeholder icon buttons --}}
        <button type="button" class="relative hidden rounded-lg p-2 hover:bg-page sm:block" title="Notifications (coming soon)" aria-label="Notifications">
            <img src="{{ asset('images/icons/bell.svg') }}" alt="" width="13.333" height="16.667">
            <span class="absolute top-1.5 right-1.5 size-2 rounded-full border-2 border-white bg-danger"></span>
        </button>
        <button type="button" class="hidden rounded-lg p-2 hover:bg-page sm:block" title="Help (coming soon)" aria-label="Help">
            <img src="{{ asset('images/icons/help.svg') }}" alt="" width="16.667" height="16.667">
        </button>

        {{-- Admin profile (static: the app has no login yet) --}}
        <div class="flex items-center gap-3 border-line sm:ml-2 sm:border-l sm:pl-[9px]">
            <span class="size-8 shrink-0 overflow-hidden rounded-full border border-line bg-avatar">
                <img src="{{ asset('images/avatar.jpg') }}" alt="Alex Johnson" class="size-full object-cover">
            </span>
            <span class="hidden flex-col xl:flex">
                <span class="text-sm leading-[14px] font-semibold tracking-[0.07px] whitespace-nowrap text-ink">Alex Johnson</span>
                <span class="text-[11px] leading-[13.75px] font-medium tracking-[0.22px] text-muted">Admin User</span>
            </span>
        </div>
    </div>
</header>
