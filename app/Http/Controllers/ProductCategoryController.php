<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index(Request $request, int $limit = null)
    {
        return response([
            "message" => "Success",
            "categories" => ProductCategory::query()->limit($limit)->get(),
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

    public function categoryProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "limit" => "integer",
            "categories" => "required|array",
            "categories.*" => "string|distinct",
        ]);

        if ($validator->fails()) {
            return response([
                "message" => "Validation failed",
                "errors" => $validator->errors(),
            ], 400);
        }

        $products = array_merge(
            ...array_map(function ($id) use ($request) {
                return ProductCategory::query()
                    ->find($id)
                    ->products()
                    ->with(["images", "categories"])
                    ->limit($request->limit)
                    ->get()
                    ->toArray();
            }, $request->categories)
        );

        $uniqueProducts = collect($products)->unique("id");

        return response([
            "message" => "Returning " . count($uniqueProducts) . " products",
            "categories" => $request->categories,
            "products" => $uniqueProducts,
        ]);
    }
}
