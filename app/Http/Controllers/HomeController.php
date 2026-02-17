<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        return view('frontend.home.index');
    }

    public function getProducts() {

        $products = Product::with('media')->latest()->get();

        return response()->json([
            'products' => $products,

            'wishlistIds' => [],
            'cartIds' => [],
            'wishlistCount' => 0,
            'cartCount' => 0
        ]);
    }

    public function show($id) {
        $product = Product::with('media')->find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $html = view('frontend.partials.quick_view', compact('product'))->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    public function getWishlistContent(Request $request)
    {
        $ids = $request->ids ?? [];

        $products = Product::with('media')->whereIn('id', $ids)->get();

        $html = view('frontend.partials.wishlist_drawer_items', compact('products'))->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    public function toggleWishlist($id)
    {
        return response()->json(['status' => 'ok']);
    }

     public function addToCart($id)
    {
        return response()->json(['status' => 'ok']);
    }

    public function getCartContent(Request $request)
    {
        $cartItems = $request->items ?? [];
        $ids = collect($cartItems)->pluck('id')->toArray();

        $products = Product::whereIn('id', $ids)->get()->map(function($product) use ($cartItems) {
            $item = collect($cartItems)->firstWhere('id', $product->id);
            $product->cart_qty = $item['qty'] ?? 1;
            return $product;
        });

        $html = view('frontend.partials.cart_drawer_items', compact('products'))->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

   public function getCheckoutDetails(Request $request)
{
    $products = $request->input('items', []);

    return response()->json([
        'status' => 'success',
        'html'   => view('frontend.partials.checkout_list', compact('products'))->render()
    ]);
}

public function getOrdersContent()
{
    return view('frontend.partials.order_history');
}
}