<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** Allowed values for the "status" column. */
    public const STATUSES = ['active', 'inactive'];

    /** Product categories shown in the filter dropdown and the create/edit form. */
    public const CATEGORIES = ['Apparel', 'Electronics', 'Home & Living', 'Accessories'];

    /** Stock at or below this number is shown as "low stock". */
    public const LOW_STOCK_THRESHOLD = 20;

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
}
