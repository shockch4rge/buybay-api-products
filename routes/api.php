<?php

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

Route::apiResources([
    "products" => ProductController::class,
]);

Route::get("/user/{id}/products", [ProductController::class, "sellerProducts"]);
Route::get("/products/search/{query}", [ProductController::class, "search"]);
