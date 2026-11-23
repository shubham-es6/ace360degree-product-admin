<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Insert sample products. The first five match the Figma design.
     * updateOrCreate() keys on the SKU, so running the seeder twice
     * updates the rows instead of failing on the unique SKU.
     */
    public function run(): void
    {
        // [name, sku, category, price, stock, status, description]
        $products = [
            // The five products shown in the Figma design, in the same order.
            ['Classic Cotton T-Shirt', 'APP-TSH-001', 'Apparel', 24.99, 142, 'active',
                'Soft 100% combed cotton crew-neck tee with a relaxed fit.'],
            ['Slim-Fit Denim Jeans', 'APP-JNS-082', 'Apparel', 59.50, 85, 'active',
                'Mid-rise stretch denim with a tapered leg.'],
            ['Ergonomic Wireless Mouse', 'ELE-MOU-019', 'Electronics', 39.99, 19, 'active',
                'Contoured 2.4 GHz wireless mouse with silent clicks.'],
            ['Matte Ceramic Coffee Mug', 'HOM-MUG-104', 'Home & Living', 14.00, 0, 'inactive',
                'Hand-glazed 350 ml stoneware mug, dishwasher safe.'],
            ['Minimalist LED Desk Lamp', 'HOM-LMP-045', 'Home & Living', 48.00, 64, 'active',
                'Dimmable desk lamp with three colour temperatures.'],

            // More sample products.
            ['Merino Wool Sweater', 'APP-SWT-014', 'Apparel', 89.00, 12, 'active',
                'Lightweight merino knit, ideal for layering.'],
            ['Waterproof Rain Jacket', 'APP-JKT-033', 'Apparel', 119.00, 0, 'inactive',
                'Packable shell jacket with sealed seams and an adjustable hood.'],
            ['Mechanical Keyboard', 'ELE-KBD-207', 'Electronics', 129.00, 48, 'active',
                'Hot-swappable 75% keyboard with tactile switches.'],
            ['Noise-Cancelling Headphones', 'ELE-HDP-310', 'Electronics', 199.99, 27, 'active',
                'Over-ear Bluetooth headphones with 30-hour battery life.'],
            ['USB-C Charging Hub', 'ELE-HUB-045', 'Electronics', 49.00, 7, 'active',
                '7-in-1 hub with 100 W pass-through charging and 4K HDMI.'],
            ['Linen Throw Pillow', 'HOM-PLW-021', 'Home & Living', 32.50, 96, 'active',
                'Stone-washed linen cover with a feather-blend insert.'],
            ['Bamboo Cutting Board', 'HOM-CTB-056', 'Home & Living', 27.00, 150, 'active',
                'Reversible organic bamboo board with juice groove.'],
            ['Leather Card Wallet', 'ACC-WAL-011', 'Accessories', 35.00, 73, 'active',
                'Slim full-grain leather wallet that holds six cards.'],
            ['Canvas Tote Bag', 'ACC-TOT-008', 'Accessories', 22.00, 210, 'active',
                'Heavy-duty cotton canvas tote with an inner pocket.'],
            ['Polarized Sunglasses', 'ACC-SUN-062', 'Accessories', 75.00, 15, 'inactive',
                'Acetate frames with UV400 polarized lenses.'],
        ];

        foreach ($products as $i => [$name, $sku, $category, $price, $stock, $status, $description]) {
            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category' => $category,
                    'stock_quantity' => $stock,
                    'status' => $status,
                    'image_path' => null,
                ],
            );

            // Each product is one hour older than the one before it, so
            // "Sort by: Latest" lists them in the order above (Figma order).
            // created_at isn't in $fillable, so it is set directly.
            $product->created_at = now()->subHours($i);
            $product->saveQuietly();
        }
    }
}
