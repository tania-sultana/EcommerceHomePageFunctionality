<?php

namespace App\Repositories;
use App\Models\Order;
use Arafat\LaravelRepository\Repository;
use App\Http\Requests\OrderStoreRequest;

class OrderRepository extends Repository
{
    public static function model()
    {
        return Order::class;
    }

    public static function storeOrder(OrderStoreRequest $request): Order
    {

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

        foreach ($request->items as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'total'      => $item['quantity'] * $item['price'],
            ]);
        }

        return $order;
    }
}
