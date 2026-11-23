<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for both "create product" (POST /products)
 * and "edit product" (PUT /products/{product}).
 *
 * Laravel runs this automatically before the controller method.
 * If a rule fails, the controller never runs: the user is sent back
 * to the form with the errors and their old input.
 */
class ProductRequest extends FormRequest
{
    /**
     * Who may send this request. There's no login in this app, so everyone.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Clean the input before the rules run.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // "  app-tsh-001 " and "APP-TSH-001" are treated as the same SKU.
            'sku' => strtoupper(trim((string) $this->input('sku'))),
            'name' => trim((string) $this->input('name')),
        ]);
    }

    public function rules(): array
    {
        // On edit, the route is /products/{product}, so this is the product being edited.
        // On create there's no {product} in the URL, so it's null.
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],

            'sku' => [
                'required',
                'string',
                'max:64',
                'regex:/^[A-Z0-9][A-Z0-9_-]*$/',
                // Must not already exist in products.sku ...
                // ... except in this product's own row when editing.
                Rule::unique('products', 'sku')->ignore($product),
            ],

            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'category' => ['required', Rule::in(Product::CATEGORIES)],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'status' => ['required', Rule::in(Product::STATUSES)],

            // max is in kilobytes: 2048 KB = 2 MB.
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Friendlier wording for some errors.
     */
    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU is already used by another product.',
            'sku.regex' => 'Use only letters, numbers, dashes and underscores.',
            'price.decimal' => 'The price can have at most 2 decimal places.',
            'price.min' => 'The price cannot be negative.',
            'stock_quantity.min' => 'Stock cannot be negative.',
            'stock_quantity.integer' => 'Stock must be a whole number.',
            'image.mimes' => 'The image must be a JPG, PNG or WEBP file.',
            'image.max' => 'The image must not be larger than 2 MB.',
            // PHP itself rejects files above upload_max_filesize before Laravel sees them.
            'image.uploaded' => 'The image must not be larger than 2 MB.',
        ];
    }

    /**
     * Field names used inside error messages ("The stock field is required.").
     */
    public function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'stock_quantity' => 'stock',
        ];
    }
}
