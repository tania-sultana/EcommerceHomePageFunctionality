<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return $this->json(
            'Products fetched successfully',
            ProductResource::collection($products)
        );
    }
}