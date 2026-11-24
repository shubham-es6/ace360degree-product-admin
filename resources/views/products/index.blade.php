@extends('layouts.app')

@section('title', 'Product List')
@section('breadcrumb', 'Products')

@section('header-actions')
    {{-- Downloads a CSV of the current search/filters/sort (page and rows-per-page are ignored). --}}
    <a href="{{ route('products.export', array_filter($filters)) }}"
       class="flex h-9 items-center gap-1.5 rounded-lg border border-line bg-white px-2.5 text-sm leading-5 font-medium tracking-[0.07px] text-ink hover:bg-page xl:px-[15px]"
       title="Download the filtered list as CSV">
        <img src="{{ asset('images/icons/export.svg') }}" alt="" width="12" height="12">
        <span class="hidden xl:inline">Export</span>
        <span class="sr-only xl:hidden">Export CSV</span>
    </a>

    <a href="{{ route('products.create', request()->query()) }}" @click.prevent="$dispatch('open-create-product')"
       class="flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3 text-sm leading-5 font-medium tracking-[0.07px] whitespace-nowrap text-white shadow-sm hover:bg-primary-dark sm:px-4">
        <img src="{{ asset('images/icons/plus.svg') }}" alt="" width="9.333" height="9.333">
        <span class="hidden sm:inline">Add Product</span>
        <span class="sm:hidden">Add</span>
    </a>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        @include('products.partials.stats')

        {{--
            Main card: toolbar + table + footer.
            "selected" holds the ticked row ids (Alpine). It only drives the checkboxes
            and row highlight – bulk actions are a placeholder.
        --}}
        <section class="overflow-hidden rounded-xl border border-line bg-white shadow-sm"
                 x-data="{ selected: [], ids: @js($products->pluck('id')->map(fn ($id) => (string) $id)) }">

            @include('products.partials.toolbar')

            @if ($products->isEmpty())
                @include('products.partials.empty-state')
            @else
                @include('products.partials.table')

                {{-- Footer: result count, rows per page, page links --}}
                <div class="flex flex-col gap-3 border-t border-line px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                        <p class="text-[13px] leading-[18px] text-muted">
                            Showing <span class="font-semibold text-ink">{{ $products->firstItem() }}</span>
                            to <span class="font-semibold text-ink">{{ $products->lastItem() }}</span>
                            of <span class="font-semibold text-ink">{{ $products->total() }}</span> products
                        </p>

                        <div class="flex items-center gap-2">
                            <label for="per_page" class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Rows per page:</label>
                            {{-- form="filters" makes this select part of the toolbar's filter form. --}}
                            <select id="per_page" name="per_page" form="filters"
                                    @change="$el.form.requestSubmit()"
                                    class="h-8 appearance-none rounded-md border border-line bg-white px-2 text-center text-[13px] text-ink focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                @foreach (\App\Models\Product::PER_PAGE_OPTIONS as $option)
                                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{ $products->onEachSide(1)->links('partials.pagination') }}
                </div>
            @endif
        </section>
    </div>

    {{-- Add Product modal. Opens when: the Add button is clicked, the URL is /products/create,
         or the Add form came back with validation errors. --}}
    @php($openCreate = ($openCreate ?? false) || (old('_form') === 'create' && $errors->any()))
    @include('products.partials.form-modal', [
        'product' => new \App\Models\Product(),
        'open' => $openCreate,
        'closeUrl' => request()->routeIs('products.create') ? route('products.index', request()->query()) : null,
    ])

    {{-- Edit modal (only on /products/{id}/edit, see edit.blade.php). --}}
    @yield('modal')

    {{-- One delete confirmation modal, shared by every row's trash button. --}}
    @include('products.partials.delete-modal')
@endsection
