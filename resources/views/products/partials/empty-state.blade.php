{{-- Shown instead of the table when there are no rows to display. --}}
<div class="flex flex-col items-center border-t border-line px-6 py-16 text-center">
    <span class="flex size-14 items-center justify-center rounded-full bg-tint text-primary">
        <x-icon :name="$hasFilters ? 'search-off' : 'box'" class="size-7" />
    </span>

    @if ($hasFilters)
        {{-- Products exist, but none match the current search/filters. --}}
        <h2 class="mt-4 text-base font-semibold text-ink">No products match your filters</h2>
        <p class="mt-1 max-w-sm text-sm text-muted">
            @if ($filters['search'] !== '')
                Nothing found for “{{ $filters['search'] }}”.
            @endif
            Try a different search term or clear the filters.
        </p>
        <a href="{{ route('products.index') }}"
           class="mt-5 inline-flex h-9 items-center gap-1.5 rounded-lg border border-line bg-white px-4 text-sm font-medium text-ink hover:bg-page">
            <x-icon name="reset" class="size-3.5" />
            Reset Filters
        </a>
    @else
        {{-- The products table is completely empty. --}}
        <h2 class="mt-4 text-base font-semibold text-ink">No products yet</h2>
        <p class="mt-1 max-w-sm text-sm text-muted">Add your first product to start building your catalogue.</p>
        <a href="{{ route('products.create', request()->query()) }}" @click.prevent="$dispatch('open-create-product')"
           class="mt-5 inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
            <img src="{{ asset('images/icons/plus.svg') }}" alt="" width="9.333" height="9.333">
            Add Product
        </a>
    @endif
</div>
