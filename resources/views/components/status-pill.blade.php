{{-- "● Active" / "● Inactive" pill. Usage: <x-status-pill :product="$product" /> --}}
@props(['product'])

@if ($product->isActive())
    <span {{ $attributes->merge(['class' => 'inline-flex h-5 items-center gap-1.5 rounded-full border border-success/15 bg-success-pill px-2.5 text-[11px] leading-[14px] font-medium text-success']) }}>
        <span class="size-1.5 rounded-full bg-success" aria-hidden="true"></span>Active
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex h-5 items-center gap-1.5 rounded-full border border-line bg-inactive-pill px-2.5 text-[11px] leading-[14px] font-medium text-subtle']) }}>
        <span class="size-1.5 rounded-full bg-subtle" aria-hidden="true"></span>Inactive
    </span>
@endif
