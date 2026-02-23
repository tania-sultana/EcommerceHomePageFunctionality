<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->id + 1000,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'subtotal' => number_format($this->subtotal, 2),
            'delivery_charge' => number_format($this->delivery_charge, 2),
            'total_amount' => number_format($this->total_amount, 2),
            'status' => $this->status ?? 'Pending',
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'name'  => $item->product->name ?? 'N/A',
                        'qty'   => $item->qty,
                        'price' => $item->price,
                        'thumbnail' => $item->product->thumbnail ?? '',
                    ];
                });
            }),
            'created_at' => $this->created_at->format('d M Y, h:i A'),
        ];
    }
}