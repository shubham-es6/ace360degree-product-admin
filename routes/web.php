<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');

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
