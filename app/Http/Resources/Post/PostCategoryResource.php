<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\BaseResource;
use App\Http\Resources\PostResource;

class PostCategoryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
