<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(ProductRequest $request)
    {
        ProductRepository::storeByRequest($request);

        return to_route('products.index')->with('success', 'Product added successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        ProductRepository::updateByRequest($request, $product);

        return to_route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $media = $product->media;

        if ($media && Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $product->delete();

        if ($media) {
            $media->delete();
        }

        return back()->with('success', 'Product deleted successfully!');
    }
}
