<?php

namespace App\Support;

use Illuminate\Support\Arr;

/**
 * The product list's state in the URL: ?search=…&category=…&status=…&sort=…&per_page=…&page=…
 *
 * Links and forms that leave the list (show, edit, delete…) carry these values along,
 * so you always come back to the same filtered page.
 */
class ProductListQuery
{
    public const KEYS = ['search', 'category', 'status', 'sort', 'per_page', 'page'];

    /**
     * Only the URL's query string – never form fields from a POST body
     * (the product form also has "category" and "status" inputs).
     */
    public static function current(): array
    {
        return Arr::only(request()->query(), self::KEYS);
    }
}
