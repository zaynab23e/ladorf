<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
/*                'image' => $this->image,*/
                'image_url' => $this->image ? asset($this->image) : null,
                'type' => $this->type,
            ],
            'relationship' => [
                'menuCategory' =>[
                    'id' => $this->menuCategory->id,
                    'name' => $this->menuCategory->name,
                    'description' => $this->menuCategory->description,
                    // 'image' => $this->menuCategory->image,
                    'image_url' => $this->menuCategory->image ? asset($this->menuCategory->image) : null,
                    ],
            ]
        ];
    }
}
