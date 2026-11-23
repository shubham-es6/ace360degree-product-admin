{{--
    /products/{id}/edit – the product list with the Edit modal open,
    using the same form partial as Add, pre-filled with this product.
--}}
@extends('products.index')

@section('title', 'Edit Product')

@section('modal')
    @include('products.partials.form-modal', [
        'product' => $product,
        'open' => true,
        'closeUrl' => route('products.index', request()->query()),
    ])
@endsection
