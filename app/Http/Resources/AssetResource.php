<?php

namespace App\Http\Resources;

use App\Models\CustomerAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CustomerAsset $asset */
        $asset = $this->resource;

        return [
            'id' => $asset->id,
            'customer_id' => $asset->customer_id,
            'name' => $asset->filename,
            'type' => $asset->mime_type,
            'url' => $asset->public_url,
            'sort_order' => data_get($asset, 'pivot.sort_order'),
            'created_at' => $asset->created_at?->toIso8601String(),
            'updated_at' => $asset->updated_at?->toIso8601String(),
        ];
    }
}
