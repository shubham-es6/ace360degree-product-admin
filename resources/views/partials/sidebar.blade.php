@php
    // Sidebar menu. "route" = null means the page doesn't exist yet (placeholder).
    // "active" is the route-name pattern that highlights the item.
    $navItems = [
        ['label' => 'Dashboard',            'icon' => 'nav-dashboard',       'route' => 'dashboard',      'active' => 'dashboard',  'w' => 15,   'h' => 15],
        ['label' => 'Product Management',   'icon' => 'nav-products-active', 'route' => 'products.index', 'active' => 'products.*', 'w' => 16.7, 'h' => 16.7],
        ['label' => 'Store Management',     'icon' => 'nav-store',           'route' => null, 'active' => null, 'w' => 15,   'h' => 13.3],
        ['label' => 'Orders',               'icon' => 'nav-orders',          'route' => null, 'active' => null, 'w' => 16.7, 'h' => 16.7],
        ['label' => 'Vendor Management',    'icon' => 'nav-vendors',         'route' => null, 'active' => null, 'w' => 18.3, 'h' => 13.3],
        ['label' => 'Promotions',           'icon' => 'nav-promotions',      'route' => null, 'active' => null, 'w' => 16.7, 'h' => 16.7],
        ['label' => 'Marketing Management', 'icon' => 'nav-marketing',       'route' => null, 'active' => null, 'w' => 16.7, 'h' => 13.3],
        ['label' => 'POS',                  'icon' => 'nav-pos',             'route' => null, 'active' => null, 'w' => 16.7, 'h' => 16.7],
    ];
@endphp

{{--
    Desktop (lg and up): always visible, fixed on the left.
    Mobile: hidden off-screen (-translate-x-full) and slides in when sidebarOpen is true.
--}}
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col justify-between overflow-y-auto border-r border-line bg-white px-4 py-4 shadow-sm transition-transform duration-200 data-open:translate-x-0 lg:translate-x-0"
       :data-open="sidebarOpen"
       aria-label="Main navigation">

    <div class="flex flex-col gap-6">
        {{-- Brand --}}
        <div class="flex items-center gap-3 px-2 pt-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary text-base font-bold tracking-tight text-white shadow-sm">A3</span>
                <span class="flex flex-col">
                    <span class="text-lg leading-[22.5px] font-bold tracking-[-0.45px] text-ink">Ace360degree</span>
                    <span class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Enterprise Suite</span>
                </span>
            </a>

            {{-- Close button, mobile only --}}
            <button type="button" class="ml-auto rounded-lg p-2 text-muted hover:bg-page lg:hidden"
                    @click="sidebarOpen = false" aria-label="Close menu">
                <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                    <path d="M5 5l10 10M15 5L5 15" />
                </svg>
            </button>
        </div>

        {{-- Menu --}}
        <nav class="flex flex-col gap-1">
            @foreach ($navItems as $item)
                @php
                    $isActive = $item['active'] && request()->routeIs($item['active']);
                    $iconStyle = "--icon: url('" . asset('images/icons/' . $item['icon'] . '.svg') . "'); width: {$item['w']}px; height: {$item['h']}px;";
                @endphp

                @if ($item['route'])
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'flex items-center gap-3 py-2 text-sm leading-5 tracking-[0.07px] transition-colors',
                           'rounded-r-lg border-l-4 border-primary-dark bg-primary-soft pr-3 pl-4 font-semibold text-primary-dark' => $isActive,
                           'rounded-lg px-3 font-medium text-muted hover:bg-page hover:text-ink' => ! $isActive,
                       ])
                       @if ($isActive) aria-current="page" @endif>
                        <span class="flex w-5 justify-center"><span class="icon" style="{{ $iconStyle }}" aria-hidden="true"></span></span>
                        {{ $item['label'] }}
                    </a>
                @else
                    {{-- Placeholder: not a real link yet. --}}
                    <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm leading-5 font-medium tracking-[0.07px] text-muted"
                          title="Coming soon" aria-disabled="true">
                        <span class="flex w-5 justify-center"><span class="icon" style="{{ $iconStyle }}" aria-hidden="true"></span></span>
                        {{ $item['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    </div>

    {{-- Footer --}}
    <div class="mt-6 flex flex-col gap-1 border-t border-line pt-4">
        <div class="flex items-center gap-3 rounded-lg px-3 py-2">
            <span class="flex w-5 justify-center">
                <img src="{{ asset('images/icons/status-ok.svg') }}" alt="" width="15" height="15">
            </span>
            <span class="flex flex-col">
                <span class="text-sm leading-[17.5px] font-medium tracking-[0.07px] text-muted">System Status</span>
                <span class="text-[11px] leading-[14px] font-medium tracking-[0.22px] text-success">All APIs Operational</span>
            </span>
        </div>

        {{-- Placeholder: the app has no login yet, so Logout does nothing. --}}
        <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2 text-sm leading-5 font-medium tracking-[0.07px] text-danger"
              title="Coming soon" aria-disabled="true">
            <span class="flex w-5 justify-center">
                <img src="{{ asset('images/icons/logout.svg') }}" alt="" width="13.5" height="13.5">
            </span>
            Logout
        </span>
    </div>
</aside>
