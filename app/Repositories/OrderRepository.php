<?php

namespace App\Repositories;

use App\Models\Order;
use Arafat\LaravelRepository\Repository;
use Illuminate\Http\Request;

class OrderRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Order::class;
    }

    public static function storeByRequest(Request $request, array $cart): Order
    {

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        $order = self::create([
            'invoice_no'      => 'INV-' . strtoupper(uniqid()),
            'name'            => $request->name,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'subtotal'        => $request->subtotal,
            'delivery_charge' => $request->delivery_charge,
            'total_amount'    => $request->total_amount,
            'status'          => 'pending',
        ]);

        foreach ($cart as $id => $item) {
            $order->items()->create([
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return $order;
    }
}