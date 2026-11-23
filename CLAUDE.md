# Ace360degree – Product Management (Internship Assignment)

Figma (visual style reference): https://www.figma.com/design/iTEhbAViSLLG046nw6H2VS/Ace-Assignment?node-id=1-674
(fileKey `iTEhbAViSLLG046nw6H2VS`, node `1:674` – "Product List" admin dashboard)

## Stack
Laravel 13 (latest stable; originally specified as Laravel 11), running on PHP 8.4,
Blade, MySQL, Tailwind CSS v4 (via Vite), Alpine.js only where needed.

Local environment (macOS, Homebrew): PHP 8.4 (`php@8.4`, linked as default `php`;
PHP 8.6 caused a `zend_mm_heap corrupted` crash, so it is installed but unlinked;
Composer `config.platform.php` is pinned to 8.4.28), Composer 2.11, MySQL 26.10 (`brew services`),
database `ace360degree`, user `root`, no password, localhost only.

## Products table
| column         | rules                              |
|----------------|------------------------------------|
| name           | required string                    |
| sku            | required, unique                   |
| description    | nullable text                      |
| price          | decimal(10,2), >= 0                |
| category       | string                             |
| stock_quantity | unsigned int                       |
| status         | `active` / `inactive`              |
| image_path     | nullable                           |
| timestamps     |                                    |

## Must have
- Product model, migration, seeder (at least 12 products), ProductController,
  `Route::resource` with named routes, Blade views (layout + index/create/edit/show)
- Full CRUD saved in DB; image upload via `storage:link`
- Search by partial name OR sku; filter by category and status; sort;
  search + filters combine; "Reset Filters" button
- Server-side `paginate()` with `withQueryString()`; "Showing X to Y of Z" count
- FormRequest validation (unique sku, ignore current product on update)
- CSRF, correct HTTP methods (PUT/DELETE), escaped `{{ }}` output only (no `{!! !!}`)
- Delete confirmation, flash messages (toast like Figma), empty state
- Sidebar: Dashboard + Product Management active, others placeholders
- Responsive: sidebar collapses on mobile, table scrolls horizontally
- README with setup steps, `.env.example`, no secrets committed
- The 3 bottom panels in Figma (Critical Stock Replenishment, Catalog SKU Breakdown,
  Store Channel Sync) are optional – build last, only if time allows

## Process
Work step by step and STOP after each step so the user can test:
1. Laravel setup + Tailwind + .env for MySQL
2. Migration + model + seeder
3. Layout (sidebar + header)
4. Index page: table, search, filters, sort, pagination, empty state
5. Create/Edit forms + validation + image upload
6. Delete confirmation + flash messages
7. README

After each step, explain the code simply so the user can explain it in their review.
