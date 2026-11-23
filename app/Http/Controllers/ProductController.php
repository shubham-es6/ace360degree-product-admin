<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Product list. Step 4 adds the table, search, filters, sorting and pagination.
     */
    public function index(): View
    {
        return view('products.index');
    }

    /** Built in Step 5. */
    public function create()
    {
        abort(501, 'Create form is added in Step 5.');
    }

    /** Built in Step 5. */
    public function store()
    {
        abort(501, 'Saving is added in Step 5.');
    }

    /** Built in Step 5. */
    public function show(Product $product)
    {
        abort(501, 'Product details are added in Step 5.');
    }

    /** Built in Step 5. */
    public function edit(Product $product)
    {
        abort(501, 'Edit form is added in Step 5.');
    }

    /** Built in Step 5. */
    public function update(Product $product)
    {
        abort(501, 'Updating is added in Step 5.');
    }

    /** Built in Step 6. */
    public function destroy(Product $product)
    {
        abort(501, 'Deleting is added in Step 6.');
    }
}
