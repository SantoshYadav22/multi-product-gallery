<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray(Request $request): array
    {
        $productPrice = $this->product?->price ?? 0;
        $total = $productPrice * $this->quantity;

        return [
            'cart_id' => $this->id,
            'quantity' => $this->quantity,
            'added_at' => $this->created_at->format('Y-m-d H:i:s'),
            'product_total' => number_format($total, 2),
            'product' => [
                'id' => $this->product->id ?? null,
                'name' => $this->product->name ?? null,
                'price' => $productPrice,
                'description' => $this->product->description ?? null,
            ],
        ];
    }

}
