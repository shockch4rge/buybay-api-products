<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return response([
            "message" => "Success",
            "categories" => ProductCategory::all(),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return response([
            "message" => "Success",
            "category" => ProductCategory::query()->find($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        ProductCategory::destroy($id);

        return response([
            "message" => "Success",
        ]);
    }

    public function categoryProducts(Request $request, string $id)
    {
        $products = ProductCategory::query()
            ->find($id)
            ->products()
            ->with(["images", "categories"])
            ->orderBy("name")
            ->limit(10)
            ->get();

        return response([
            "message" => "Returning " . $products->count() . " products",
            "products" => $products,
        ]);
    }
}
