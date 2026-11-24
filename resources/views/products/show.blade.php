@extends('layouts.app')

@section('title', $product->name)
@section('breadcrumb', 'Products')

@php
    // Back / Edit / Delete keep the list's filters and page from the URL.
    $listQuery = \App\Support\ProductListQuery::current();
@endphp

@section('content')
    <div class="mx-auto flex max-w-5xl flex-col gap-4">

        <a href="{{ route('products.index', $listQuery) }}"
           class="inline-flex items-center gap-1.5 self-start rounded-lg px-2 py-1 -ml-2 text-sm font-medium text-muted hover:bg-white hover:text-ink">
            <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 15l-5-5 5-5" />
            </svg>
            Back to Product List
        </a>

        <article class="overflow-hidden rounded-xl border border-line bg-white shadow-sm">
            {{-- Title bar --}}
            <div class="flex flex-col gap-4 border-b border-line px-5 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-6">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl leading-7 font-bold tracking-[-0.3px] break-words text-ink">{{ $product->name }}</h2>
                        <x-status-pill :product="$product" />
                    </div>
                    <p class="mt-1 font-mono text-xs text-muted">SKU: {{ $product->sku }}</p>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <a href="{{ route('products.edit', [$product] + $listQuery) }}"
                       class="flex h-9 items-center gap-1.5 rounded-lg bg-primary px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                        <x-icon name="pencil" class="size-4" />
                        Edit
                    </a>
                    <x-delete-button :product="$product"
                                     class="flex h-9 items-center gap-1.5 rounded-lg border border-danger/30 bg-white px-4 text-sm font-medium text-danger hover:bg-danger-soft">
                        <x-icon name="trash" class="size-4" />
                        Delete
                    </x-delete-button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 sm:p-6 md:grid-cols-[260px_1fr]">
                {{-- Image --}}
                <div class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-xl border border-line bg-page text-subtle md:w-[260px]">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="size-full object-cover">
                    @else
                        <div class="flex flex-col items-center gap-2 text-center">
                            <x-icon name="image" class="size-10" />
                            <span class="text-xs">No image uploaded</span>
                        </div>
                    @endif
                </div>

                <div class="flex min-w-0 flex-col gap-6">
                    {{-- Key facts --}}
                    <dl class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <div class="rounded-lg border border-line/70 bg-page px-4 py-3">
                            <dt class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Price</dt>
                            <dd class="mt-1 text-xl leading-7 font-bold text-ink">${{ number_format($product->price, 2) }}</dd>
                        </div>

                        <div class="rounded-lg border border-line/70 bg-page px-4 py-3">
                            <dt class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Stock</dt>
                            <dd @class([
                                'mt-1 text-xl leading-7 font-bold',
                                'text-danger' => $product->isLowStock(),
                                'text-subtle' => $product->isOutOfStock(),
                                'text-ink' => ! $product->isLowStock() && ! $product->isOutOfStock(),
                            ])>
                                {{ number_format($product->stock_quantity) }}
                                <span class="text-xs font-medium text-muted">{{ $product->stock_quantity === 1 ? 'unit' : 'units' }}</span>
                            </dd>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-tint" aria-hidden="true">
                                <div @class(['h-full rounded-full', 'bg-danger' => $product->isLowStock(), 'bg-success' => ! $product->isLowStock()])
                                     style="width: {{ $product->stockPercent() }}%"></div>
                            </div>
                            @if ($product->isLowStock())
                                <p class="mt-1.5 text-[11px] font-medium text-danger">Low stock – needs restock</p>
                            @elseif ($product->isOutOfStock())
                                <p class="mt-1.5 text-[11px] font-medium text-muted">Out of stock</p>
                            @endif
                        </div>

                        <div class="rounded-lg border border-line/70 bg-page px-4 py-3">
                            <dt class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Category</dt>
                            <dd class="mt-2">
                                <span class="inline-block rounded-md border border-line/50 bg-tint px-2 py-0.5 text-xs leading-4 text-muted">{{ $product->category }}</span>
                            </dd>
                        </div>

                        <div class="rounded-lg border border-line/70 bg-page px-4 py-3">
                            <dt class="text-[11px] leading-[14px] font-medium tracking-[0.55px] text-muted uppercase">Product ID</dt>
                            <dd class="mt-1 font-mono text-xl leading-7 font-bold text-ink">#{{ $product->id }}</dd>
                        </div>
                    </dl>

                    {{-- Description: plain, escaped text; the CSS class whitespace-pre-line keeps its line breaks (no raw HTML needed) --}}
                    <section>
                        <h3 class="text-[13px] font-semibold text-ink">Description</h3>
                        @if (filled($product->description))
                            <p class="mt-1.5 text-sm leading-6 break-words whitespace-pre-line text-muted">{{ $product->description }}</p>
                        @else
                            <p class="mt-1.5 text-sm text-subtle italic">No description provided.</p>
                        @endif
                    </section>

                    <dl class="flex flex-wrap gap-x-8 gap-y-2 border-t border-line pt-4 text-xs text-muted">
                        <div>
                            <dt class="inline">Created:</dt>
                            <dd class="inline font-medium text-ink">{{ $product->created_at?->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="inline">Last updated:</dt>
                            <dd class="inline font-medium text-ink" title="{{ $product->updated_at?->format('d M Y, H:i') }}">{{ $product->updated_at?->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </article>
    </div>

    @include('products.partials.delete-modal')
@endsection
