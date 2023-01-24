<?php

namespace App\Http\Controllers;

use App\Jobs\FetchReviewsJob;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return response([
            "message" => "Success",
            "products" => Product::with(["images", "categories"])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "seller_id" => "required",
            "name" => "required|string",
            "description" => "required|string",
            "price" => "required|numeric",
            "quantity" => "required|numeric",
            "images" => "required",
            "images.*" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "categories" => "array",
            "categories.*" => "string|distinct",
        ]);

        if ($validation->fails()) {
            return response([
                "message" => "Validation failed",
                "errors" => $validation->errors(),
            ], 400);
        }

        $product = Product::query()->create([
            "seller_id" => $request->seller_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        if ($request->hasFile("images")) {
            $disk = Storage::disk();
            $images = $request->file("images");

            array_walk($images, function ($file, $i) use ($product, $disk) {
                $s3Path = $product->id . "/image_" . $i . $file->getClientOriginalExtension();

                return ProductImage::query()->create([
                    "product_id" => $product->id,
                    "url" => $disk->url($disk->put($s3Path, file_get_contents($file))),
                ]);
            });
        }

        array_walk($request->categories, function ($value) use ($product) {
            $query = ProductCategory::query();

            // if passed in an id
            if ($query->find($value)) {
                $product->categories()->attach($value);
                return;
            }

            $query->create([
                "product_id" => $product->id,
                "name" => $value,
            ]);
        });

        return response([
            "message" => "Success",
            "product" => $product->with(["images", "categories"]),
        ]);
    }

    public function show($id)
    {
        $product = Product::with(["images", "categories"])->find($id);

        if (!$product) {
            return response([
                "message" => "Could not find product with requested id"
            ], 400);
        }

        return response([
            "message" => "Success",
            "product" => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            "seller_id" => "string",
            "name" => "string",
            "description" => "string",
            "price" => "numeric",
            "quantity" => "numeric",
            "images.*" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "categories" => "array",
            "categories.*" => "string|distinct",
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response([
                "message" => "Could not find product with requested id"
            ], 400);
        }

        $product->update($request->all());

        return response([
            "message" => "Updated product id: " . $product->id,
            "product" => $product,
        ]);
    }

    public function destroy($id)
    {
        Product::destroy($id);

        return response([
            "message" => "Product deleted",
        ]);
    }

    public function sellerProducts(Request $request, $user_id) {
        $products = Product::query()
            ->where("seller_id", $user_id)
            ->with(["images", "categories"])
            ->get();

        $reviews = FetchReviewsJob::dispatchSync($products);

        echo $reviews;

        return response([
            "message" => "Returning " . $products->count() . " products",
            "products" => $products,
        ]);
    }

    public function search(
        Request $request,
        string $query,
        $includeProducts,
        $includeCategories,
        $limit = null,
    )
    {
        if (!$includeCategories && !$includeProducts) {
            return response([
                "message" => "Must include either categories or products",
            ], 400);
        }

        $body = [
            "message" => "Returning search results",
        ];

        if ($includeProducts) {
            $body["products"] = Product::query()
                ->where("name", "like", "%$query%")
                ->orWhere("description", "like", "%$query%")
                ->with(["images", "categories"])
                ->when(isset($limit), fn ($query, $limit) => $query->limit($limit))
                ->get();
        }

        if ($includeCategories) {
            $body["categories"] = ProductCategory::query()
                ->where("name", "like", "%$query%")
                ->when(isset($limit), fn ($query, $limit) => $query->limit($limit))
                ->get();
        }

        return response($body);
    }
}
