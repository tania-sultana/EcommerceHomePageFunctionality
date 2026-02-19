<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
{
    $products = Product::latest()->get();

    if ($request->ajax()) {
        return response()->json([
            'message' => 'Products fetched successfully',
            'data' => ProductResource::collection($products)
        ]);
    }

    return view('frontend.home.index');
}
}
