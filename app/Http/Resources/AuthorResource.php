<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $author */
        $author = $this->resource;

        return [
            'id' => $author->id,
            'name' => $author->name,
            'email' => $author->email,
        ];
    }
}
