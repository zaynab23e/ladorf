<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenusCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'attributes' =>[
                'name' =>$this->name,
                'description' =>$this->description,
                // 'image' => $this->image,
                'image_url' => $this->image ? asset($this->image) : null, 
            ],
            'relationship' => [
                'Items' => $this->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'type' => $item->type,
                        'is_available' => $item->is_available,
                        'menucategory_id' => $item->menucategory_id,
                        // 'image' => $item->image,
                        'image_url' => $item->image ? asset($item->image) : null,
                    ];
                }),

            ]
        ];
    }
}
