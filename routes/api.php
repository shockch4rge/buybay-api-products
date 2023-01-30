<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource("products", ProductController::class);
Route::apiResource("categories", ProductCategoryController::class)->except(["index"]);

Route::get("/user/{id}/products", [ProductController::class, "userProducts"]);
Route::get("/products/search/{query}/{products?}/{categories?}/{limit?}", [ProductController::class, "search"]);
Route::get("/categories/{limit?}", [ProductCategoryController::class, "index"]);
Route::post("/categories/products", [ProductCategoryController::class, "categoryProducts"]);
