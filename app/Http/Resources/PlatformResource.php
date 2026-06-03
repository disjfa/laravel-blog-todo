<?php

namespace App\Http\Resources;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Platform $platform */
        $platform = $this->resource;

        return [
            'id' => $platform->id,
            'name' => $platform->name,
            'slug' => $platform->slug,
            'is_active' => $platform->is_active,
        ];
    }
}
