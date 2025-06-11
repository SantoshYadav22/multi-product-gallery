<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'price'      => $this->price,
            'images'     => $this->whenLoaded('images', function () {
                if ($this->images->isNotEmpty()) {
                    return $this->images->map(function ($image) {
                        return [
                            'id'         => $image->id,
                            'product_id' => $image->product_id,
                            'url'        => asset('storage/' . $image->image_path),
                        ];  
                    });
                }
                return [];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
