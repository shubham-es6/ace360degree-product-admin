<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Product list with search, filters, sorting and pagination.
     * Everything comes from the URL query string, e.g.
     * /products?search=mug&category=Home+%26+Living&status=active&sort=price_asc&per_page=25&page=2
     */
    public function index(Request $request): View
    {
        return view('products.index', $this->listData($request));
    }

    /**
     * Add Product form. Shown as a modal on top of the product list
     * (the Add Product button also opens it instantly without a page load).
     */
    public function create(Request $request): View
    {
        return view('products.create', $this->listData($request) + ['openCreate' => true]);
    }

    /**
     * Save a new product. ProductRequest has already validated everything
     * by the time this runs.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);

        if ($request->hasFile('image')) {
            // Saved to storage/app/public/products/<random-name>.jpg,
            // visible at /storage/products/<random-name>.jpg thanks to storage:link.
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()
            ->route('products.index', $this->listQuery($request))
            ->with('toast', [
                'title' => "Product '{$product->name}' created successfully",
                'message' => 'It is now listed in your product catalogue',
            ]);
    }

    /** Built in Step 6 (with the delete flow). */
    public function show(Product $product)
    {
        abort(501, 'Product details are added later.');
    }

    /**
     * Edit form – the same modal and form partial as create, pre-filled with the product.
     * Laravel finds the product from the {product} id in the URL (route model binding)
     * and shows a 404 page if it doesn't exist.
     */
    public function edit(Request $request, Product $product): View
    {
        return view('products.edit', $this->listData($request) + ['product' => $product]);
    }

    /**
     * Save changes to an existing product.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        $oldImage = $product->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        $product->update($data);

        // Delete the old file only after the database was updated successfully.
        if ($oldImage && $oldImage !== $product->image_path) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('products.index', $this->listQuery($request))
            ->with('toast', [
                'title' => "Product '{$product->name}' updated successfully",
                'message' => 'Changes synchronized to multi-channel inventory',
            ]);
    }

    /**
     * Download the products currently shown by the filters as a CSV file.
     * Uses exactly the same filters and sort as index(), but without pagination.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->filteredQuery($this->filtersFrom($request));
        $filename = 'products-' . now()->format('Y-m-d-His') . '.csv';

        // streamDownload writes the file row by row instead of building it in memory.
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows "–", "é" etc. correctly

            fputcsv($out, ['ID', 'Name', 'SKU', 'Category', 'Price', 'Stock Quantity', 'Status', 'Description', 'Created At', 'Updated At']);

            // lazy() loads 1000 rows at a time, so a large catalogue doesn't use lots of memory.
            foreach ($query->lazy() as $product) {
                fputcsv($out, array_map([$this, 'csvSafe'], [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category,
                    $product->price,
                    $product->stock_quantity,
                    $product->status,
                    $product->description,
                    $product->created_at?->toDateTimeString(),
                    $product->updated_at?->toDateTimeString(),
                ]));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }






    /** Built in Step 6. */
    public function destroy(Product $product)
    {
        abort(501, 'Deleting is added in Step 6.');
    }

    /* ------------------------------------------------------------------ */

    /**
     * Everything the product list page needs. Used by index(), create() and edit()
     * so the list behind the modal looks exactly like the normal list.
     */
    private function listData(Request $request): array
    {
        $filters = $this->filtersFrom($request);

        $perPage = in_array((int) $request->query('per_page'), Product::PER_PAGE_OPTIONS, true)
            ? (int) $request->query('per_page')
            : 10;

        $products = $this->filteredQuery($filters)
            ->paginate($perPage)        // LIMIT/OFFSET in SQL + a COUNT(*) for the total
            ->withQueryString();        // page links keep ?search=...&category=... etc.

        // Numbers for the four stat cards (whole catalogue, not just the filtered rows),
        // calculated by MySQL in a single query.
        $stats = Product::query()->toBase()->selectRaw(
            'COUNT(*) AS total,
             COALESCE(SUM(status = ?), 0) AS active,
             COALESCE(SUM(stock_quantity BETWEEN 1 AND ?), 0) AS low_stock,
             COALESCE(SUM(stock_quantity = 0), 0) AS out_of_stock,
             COALESCE(SUM(created_at >= ?), 0) AS added_last_30_days',
            ['active', Product::LOW_STOCK_THRESHOLD, now()->subDays(30)]
        )->first();

        // True when at least one filter is in use (shows "Reset Filters", changes the empty state).
        $hasFilters = $filters['search'] !== '' || $filters['category'] !== '' || $filters['status'] !== '';

        return compact('products', 'filters', 'perPage', 'stats', 'hasFilters');
    }

    /**
     * The list's query string (search, filters, sort, page...) without anything else,
     * so after saving you land back on the same filtered page you came from.
     */
    private function listQuery(Request $request): array
    {
        // query() = only the URL's ?a=b part. (The form body also has "category" and "status" fields,
        // which must not be mistaken for list filters.)
        return Arr::only($request->query(), ['search', 'category', 'status', 'sort', 'per_page', 'page']);
    }


    /**
     * Read the filters from the URL. Anything not on an allowed list is ignored,
     * so a hand-edited URL can't break the query.
     *
     * @return array{search: string, category: string, status: string, sort: string}
     */
    private function filtersFrom(Request $request): array
    {
        $category = $request->query('category');
        $status = $request->query('status');
        $sort = (string) $request->query('sort');

        return [
            'search'   => mb_substr(trim((string) $request->query('search', '')), 0, 100),
            'category' => in_array($category, Product::CATEGORIES, true) ? $category : '',
            'status'   => in_array($status, Product::STATUSES, true) ? $status : '',
            'sort'     => array_key_exists($sort, Product::SORTS) ? $sort : 'latest',
        ];
    }

    /**
     * One query for both the table and the CSV export. Each scope only adds a
     * WHERE when its filter is filled in, so search + category + status combine (AND).
     */
    private function filteredQuery(array $filters): Builder
    {
        return Product::query()
            ->search($filters['search'])
            ->filterCategory($filters['category'])
            ->filterStatus($filters['status'])
            ->sortBy($filters['sort']);
    }

    /**
     * Stop "CSV injection": a cell starting with = + - @ could run as a formula
     * when the file is opened in Excel, so prefix it with an apostrophe.
     */
    private function csvSafe(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
