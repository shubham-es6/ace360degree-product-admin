@php
    $selectClass = 'h-9 appearance-none rounded-lg border border-line bg-white pr-7 pl-3 text-[13px] text-ink focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none';
    $showReset = $hasFilters || $filters['sort'] !== 'latest' || $perPage !== 10;
@endphp

<div class="flex flex-col gap-3 p-4 lg:flex-row lg:items-center lg:justify-between">
    {{--
        One GET form holds every filter, so search + category + status + sort are
        always sent together and combine. Dropdowns submit as soon as they change;
        the search box submits on Enter. No "page" field, so changing a filter
        goes back to page 1.
    --}}
    <form id="filters" method="GET" action="{{ route('products.index') }}" role="search"
          class="flex flex-wrap items-center gap-3 lg:max-w-[601px] lg:flex-1">

        <div class="relative w-full sm:w-[378px] lg:w-auto lg:flex-1 lg:basis-[300px]">
            <label for="search" class="sr-only">Filter by SKU or title</label>
            <img src="{{ asset('images/icons/search.svg') }}" alt="" width="12" height="12"
                 class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2">
            <input id="search" type="search" name="search" value="{{ $filters['search'] }}"
                   placeholder="Filter by SKU or title..." maxlength="100"
                   class="h-9 w-full rounded-lg border border-line bg-white pr-3 pl-[37px] text-[13px] text-ink placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
        </div>

        <div class="relative">
            <label for="category" class="sr-only">Category</label>
            <select id="category" name="category" class="{{ $selectClass }}" @change="$el.form.requestSubmit()">
                <option value="">All Categories</option>
                @foreach (\App\Models\Product::CATEGORIES as $category)
                    <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-2 size-4 -translate-y-1/2 text-muted" />
        </div>

        <div class="relative">
            <label for="status" class="sr-only">Status</label>
            <select id="status" name="status" class="{{ $selectClass }}" @change="$el.form.requestSubmit()">
                <option value="">All Status</option>
                @foreach (\App\Models\Product::STATUSES as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-2 size-4 -translate-y-1/2 text-muted" />
        </div>

        <div class="relative">
            <label for="sort" class="sr-only">Sort by</label>
            <select id="sort" name="sort" class="{{ $selectClass }}" @change="$el.form.requestSubmit()">
                @foreach (\App\Models\Product::SORTS as $key => [$label])
                    <option value="{{ $key }}" @selected($filters['sort'] === $key)>Sort by: {{ $label }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-2 size-4 -translate-y-1/2 text-muted" />
        </div>

        @if ($showReset)
            {{-- Reset = load the page with no query string at all. --}}
            <a href="{{ route('products.index') }}"
               class="flex h-9 items-center gap-1.5 rounded-lg px-3 text-[13px] font-medium text-primary hover:bg-primary-soft">
                <x-icon name="reset" class="size-3.5" />
                Reset Filters
            </a>
        @endif

        {{-- The "Rows per page" select (footer) isn't shown when the list is empty,
             so keep the chosen value here; otherwise changing a filter would reset it to 10. --}}
        @if ($products->isEmpty() && $perPage !== 10)
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        @endif

        {{-- Lets keyboard / screen-reader users submit the search explicitly. --}}
        <button type="submit" class="sr-only">Apply filters</button>
    </form>

    <div class="flex items-center gap-3">
        {{-- Placeholder: bulk actions aren't part of the assignment. --}}
        <div class="relative w-full lg:w-[186px]" title="Bulk actions (coming soon)">
            <label for="bulk" class="sr-only">Bulk actions</label>
            <select id="bulk" disabled
                    class="h-9 w-full cursor-not-allowed appearance-none rounded-lg border border-tint bg-primary-soft pr-8 pl-[13px] text-[13px] font-medium text-ink">
                <option>Bulk Actions</option>
            </select>
            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-2 size-4 -translate-y-1/2 text-muted" />
        </div>

        <a href="{{ route('products.create', request()->query()) }}" @click.prevent="$dispatch('open-create-product')"
           class="flex h-9 shrink-0 items-center gap-1.5 rounded-lg bg-primary px-4 text-sm leading-5 font-medium tracking-[0.07px] whitespace-nowrap text-white shadow-sm hover:bg-primary-dark">
            <img src="{{ asset('images/icons/plus.svg') }}" alt="" width="9.333" height="9.333">
            Add Product
        </a>
    </div>
</div>
