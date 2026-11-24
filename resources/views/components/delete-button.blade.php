{{--
    A button that opens THE delete confirmation modal (there is only one on the page)
    and fills it with this product's details.
    Usage: <x-delete-button :product="$product" class="...">Delete</x-delete-button>

    It sends a browser event "confirm-delete" with the product data;
    products/partials/delete-modal.blade.php listens for it.
--}}
@props(['product'])

@php
    $details = [
        'id' => $product->id,
        'name' => $product->name,
        'sku' => $product->sku,
        'price' => '$' . number_format($product->price, 2),
        'category' => $product->category,
        'stock' => $product->stock_quantity,
        'stockWarning' => $product->isLowStock() || $product->isOutOfStock(),
        'imageUrl' => $product->imageUrl(),
        // The form posts here (DELETE /products/{id}), keeping the list's filters and page.
        'action' => route('products.destroy', [$product] + \App\Support\ProductListQuery::current()),
    ];
@endphp

<button type="button" {{ $attributes }} @click="$dispatch('confirm-delete', @js($details))">
    {{ $slot }}
</button>
