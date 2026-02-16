<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Product;
use App\Models\Wishlist;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        $wishlistIds = Wishlist::pluck('product_id')->toArray();

        return view('frontend.home.index', compact('products', 'wishlistIds'));
    }

    public function show(Product $product)
    {
        $wishlistIds = Wishlist::pluck('product_id')->toArray();

        return view('frontend.home.details', compact('product', 'wishlistIds'));
    }

    public function wishlist()
    {
        $wishlistItems = Wishlist::with('product.media')->latest()->get();

        return view('frontend.home.wishlist', compact('wishlistItems'));
    }

    public function toggleWishlist(Product $product)
    {
        $wishlist = Wishlist::where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();

            return back()->with('success', 'Removed from Wishlist');
        }

        Wishlist::create(['product_id' => $product->id]);

        return back()->with('success', 'Added to Wishlist');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);

        return view('frontend.home.cart', compact('cart'));
    }

    public function addToCart(Product $product, Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            return back()->with('info', 'Product already in cart. You can change quantity from the cart page.');
        }

        $cart[$product->id] = [
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
            'thumbnail' => $product->thumbnail,
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    public function updateCart(Request $request, $id)
    {
        if ($id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return back()->with('success', 'Cart updated!');
        }
    }

    public function removeCart($id)
    {
        if ($id) {
            $cart = session()->get('cart');
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }

            return back()->with('success', 'Product removed!');
        }
    }

    public function checkout(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $quantity = $request->qty ?? 1;

        return view('frontend.home.checkout', compact('product', 'quantity'));
    }

    public function placeOrder(OrderStoreRequest $request)
    {
        $cart = session()->get('cart');

        if (! $cart) {
            return to_route('index')->with('error', 'Your cart is empty!');
        }

        OrderRepository::storeByRequest($request, $cart);

        session()->forget('cart');

        return to_route('index')->with('success', 'Your order has been placed successfully!');
    }

    public function orders()
    {
        $orders = OrderRepository::query()->latest()->get();

        return view('frontend.home.orders', compact('orders'));
    }
}
