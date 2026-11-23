<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');

// CSV download of the current filtered list. Registered BEFORE the resource,
// otherwise /products/export would be treated as /products/{product} with id "export".
Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

/*
 * One line registers all 7 CRUD routes, each with a name:
 *   GET     /products                 products.index
 *   GET     /products/create          products.create
 *   POST    /products                 products.store
 *   GET     /products/{product}       products.show
 *   GET     /products/{product}/edit  products.edit
 *   PUT     /products/{product}       products.update
 *   DELETE  /products/{product}       products.destroy
 */
Route::resource('products', ProductController::class);
