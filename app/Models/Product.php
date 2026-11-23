<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** Allowed values for the "status" column. */
    public const STATUSES = ['active', 'inactive'];

    /** Product categories shown in the filter dropdown and the create/edit form. */
    public const CATEGORIES = ['Apparel', 'Electronics', 'Home & Living', 'Accessories'];

    /** Stock between 1 and this number is shown as "low stock". */
    public const LOW_STOCK_THRESHOLD = 20;

    /** Stock level that fills the inventory bar to 100%. */
    public const FULL_STOCK_LEVEL = 180;

    /**
     * Sort options for the product list: key in the URL => [label, column, direction].
     * Only these columns can ever be sorted on, so the URL can't inject SQL.
     */
    public const SORTS = [
        'latest'     => ['Latest',              'created_at',     'desc'],
        'oldest'     => ['Oldest',              'created_at',     'asc'],
        'name_asc'   => ['Name (A–Z)',          'name',           'asc'],
        'name_desc'  => ['Name (Z–A)',          'name',           'desc'],
        'price_asc'  => ['Price (low to high)', 'price',          'asc'],
        'price_desc' => ['Price (high to low)', 'price',          'desc'],
        'stock_asc'  => ['Stock (low to high)', 'stock_quantity', 'asc'],
        'stock_desc' => ['Stock (high to low)', 'stock_quantity', 'desc'],
    ];

    /** Choices for the "Rows per page" dropdown. */
    public const PER_PAGE_OPTIONS = [5, 10, 25, 50];

    /**
     * Columns that may be filled with create() / update().
     * Anything not listed here (id, timestamps) is protected from mass assignment.
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'category',
        'stock_quantity',
        'status',
        'image_path',
    ];

    /**
     * Convert database values to the right PHP types automatically.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------
     | Query scopes – reusable pieces of a query.
     | Used in the controller as Product::search($term)->filterCategory($cat)->filterStatus($status)...
     | Each one does nothing when its value is empty, so they can be chained.
     * ----------------------------------------------------------------- */

    /** Partial match on name OR sku. */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        // Escape LIKE wildcards so typing "%" or "_" searches for that character literally.
        $like = '%' . addcslashes($term, '%_\\') . '%';

        // The closure wraps the OR in brackets: (name LIKE ? OR sku LIKE ?)
        // so it combines correctly with the category/status filters (AND).
        $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
              ->orWhere('sku', 'like', $like);
        });
    }

    #[Scope]
    protected function filterCategory(Builder $query, ?string $category): void
    {
        if (filled($category)) {
            $query->where('category', $category);
        }
    }

    #[Scope]
    protected function filterStatus(Builder $query, ?string $status): void
    {
        if (filled($status)) {
            $query->where('status', $status);
        }
    }

    #[Scope]
    protected function sortBy(Builder $query, string $sort): void
    {
        [, $column, $direction] = self::SORTS[$sort] ?? self::SORTS['latest'];

        // id is a tie-breaker so rows with the same value keep a stable order across pages.
        $query->orderBy($column, $direction)->orderBy('id', $direction);
    }

    /* ------------------------------------------------------------------
     | Helpers used by the views.
     * ----------------------------------------------------------------- */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= self::LOW_STOCK_THRESHOLD;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity === 0;
    }

    /** Width of the inventory bar, 0–100. */
    public function stockPercent(): int
    {
        return (int) min(100, round($this->stock_quantity / self::FULL_STOCK_LEVEL * 100));
    }

    /** Public URL of the uploaded image, or null. */
    public function imageUrl(): ?string
    {
        // asset() uses the current host, so it works on 127.0.0.1 and localhost alike.
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
