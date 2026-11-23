@php
    $total = (int) $stats->total;
    $active = (int) $stats->active;
    $coverage = $total > 0 ? round($active / $total * 100, 1) : 0;
    $iconUrl = fn (string $file) => "--icon: url('" . asset("images/icons/{$file}.svg") . "')";
@endphp

{{-- Four summary cards. Numbers come from one SQL query in ProductController@index. --}}
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Product summary">

    {{-- Total products --}}
    <div class="flex min-h-32 items-center justify-between gap-2 rounded-xl border border-line bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-1">
            <p class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Total Products</p>
            <p class="text-2xl leading-8 font-bold tracking-[-0.6px] text-ink">{{ number_format($total) }}</p>
            <p class="flex items-start gap-1.5 text-[11px] leading-[14px] font-medium text-success">
                <x-icon name="trend-up" class="mt-px size-3" />
                +{{ number_format($stats->added_last_30_days) }} added in last 30 days
            </p>
        </div>
        <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
            <span class="icon size-5" style="{{ $iconUrl('nav-products-active') }}" aria-hidden="true"></span>
        </span>
    </div>

    {{-- Active products --}}
    <div class="flex min-h-32 items-center justify-between gap-2 rounded-xl border border-line bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-1">
            <p class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Active Products</p>
            <p class="text-2xl leading-8 font-bold tracking-[-0.6px] text-ink">{{ number_format($active) }}</p>
            <p class="flex items-center gap-1.5 text-[11px] leading-[14px] font-medium text-muted">
                <span class="size-1.5 rounded-full bg-success" aria-hidden="true"></span>
                {{ $coverage }}% listing coverage
            </p>
        </div>
        <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-success-soft text-success">
            <span class="icon size-5" style="{{ $iconUrl('status-ok') }}" aria-hidden="true"></span>
        </span>
    </div>

    {{-- Low stock --}}
    <div class="flex min-h-32 items-center justify-between gap-2 rounded-xl border border-line bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-1">
            <p class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-danger uppercase">Low Stock Alert</p>
            <p class="text-2xl leading-8 font-bold tracking-[-0.6px] text-danger">{{ number_format($stats->low_stock) }}</p>
            <p class="flex items-center gap-1.5 text-[11px] leading-[14px] font-medium text-danger">
                <x-icon name="warning" class="size-3" />
                {{ $stats->low_stock > 0 ? 'Needs urgent restock' : 'All stock levels healthy' }}
            </p>
        </div>
        <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-danger-soft text-danger">
            <span class="icon size-5" style="{{ $iconUrl('nav-orders') }}" aria-hidden="true"></span>
        </span>
    </div>

    {{-- Out of stock --}}
    <div class="flex min-h-32 items-center justify-between gap-2 rounded-xl border border-line bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-1">
            <p class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Out of Stock</p>
            <p class="text-2xl leading-8 font-bold tracking-[-0.6px] text-muted">{{ number_format($stats->out_of_stock) }}</p>
            <p class="flex items-center gap-1.5 text-[11px] leading-[14px] font-medium text-muted">
                <x-icon name="ban" class="size-3" />
                Hidden from storefront
            </p>
        </div>
        <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-tint text-muted">
            <span class="icon size-5" style="{{ $iconUrl('nav-orders') }}" aria-hidden="true"></span>
        </span>
    </div>
</section>
