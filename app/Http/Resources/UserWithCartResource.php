<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CartResource;

class UserWithCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalAmount = $this->cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product?->price ?? 0) * $item->quantity;
        }, 0);

        return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'cart_items' => CartResource::collection($this->cartItems),
        'total_amount' => number_format($totalAmount, 2), 
    ];
    }
}
