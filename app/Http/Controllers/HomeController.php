<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\WishListResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest()->get();

        if ($request->ajax()) {
            return $this->json(
                'Products fetched successfully',
                ProductResource::collection($products)
            );
        }

        return view('frontend.home.index');
    }

    public function show(Product $product)
    {
        return $this->json(
            'Product fetched successfully',
            new ProductResource($product)
        );
    }

    public function toggle(Request $request, Product $product)
    {
        $action = $request->action;
        $message = ($action === 'add') ? "Added to wishlist" : "Removed from wishlist";

        return $this->json(
            $message,
            new ProductResource($product)
        );
    }

    public function cartSync(Request $request, Product $product)
    {
        return $this->json(
            'Cart synced successfully',
            new ProductResource($product)
        );
    }

    public function checkout(Request $request)
    {
        return $this->json(
            'Order placed successfully!',
            $request->all()
        );
    }
}
