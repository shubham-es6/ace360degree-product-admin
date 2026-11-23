@extends('layouts.app')

@section('title', 'Product List')
@section('breadcrumb', 'Products')

@section('header-actions')
    {{-- Placeholder: export isn't part of the assignment. --}}
    <button type="button" class="hidden h-9 cursor-not-allowed items-center gap-1.5 rounded-lg border border-line bg-white px-[15px] text-sm leading-5 font-medium tracking-[0.07px] text-ink xl:flex"
            title="Export (coming soon)" aria-disabled="true">
        <img src="{{ asset('images/icons/export.svg') }}" alt="" width="12" height="12">
        Export
    </button>

    <a href="{{ route('products.create') }}"
       class="flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3 text-sm leading-5 font-medium tracking-[0.07px] whitespace-nowrap text-white shadow-sm hover:bg-primary-dark sm:px-4">
        <img src="{{ asset('images/icons/plus.svg') }}" alt="" width="9.333" height="9.333">
        <span class="hidden sm:inline">Add Product</span>
        <span class="sm:hidden">Add</span>
    </a>
@endsection

@section('content')
    {{-- Temporary content: Step 4 replaces this with the product table. --}}
    <div class="rounded-xl border border-line bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Product list</h2>
        <p class="mt-1 text-sm text-muted">
            The product table, search, filters and pagination are added in Step 4.
            There are currently {{ \App\Models\Product::count() }} products in the database.
        </p>
    </div>
@endsection
