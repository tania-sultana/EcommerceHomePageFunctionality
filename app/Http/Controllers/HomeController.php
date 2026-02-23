<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\WishListResource;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

    public function checkout(OrderStoreRequest $request)
    {
        DB::beginTransaction();

        try {

            // Generate invoice number
            $invoiceNo = 'INV-' . strtoupper(uniqid());

            $order = Order::create([
                'invoice_no' => $invoiceNo,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'subtotal' => $request->subtotal,
                'delivery_charge' => $request->delivery_charge,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $order->id
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function orderList()
    {
        $orders = Order::latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Orders fetched successfully',
            'data' => OrderResource::collection($orders)
        ]);
    }

public function orderDetails($id)
{
    $order = Order::with('items.product')->find($id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => new OrderResource($order)
    ]);
}
    public function orderDelete($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->delete();
            return response()->json(['message' => 'Deleted']);
        }
    }
}