@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="rounded-xl border border-line bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Welcome to Ace360degree</h2>
        <p class="mt-1 text-sm text-muted">Manage your catalogue from the Product Management section.</p>
        <a href="{{ route('products.index') }}"
           class="mt-4 inline-flex h-9 items-center rounded-lg bg-primary px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
            Go to Product Management
        </a>
    </div>
@endsection
