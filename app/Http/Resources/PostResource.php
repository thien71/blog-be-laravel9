<?php

namespace App\Http\Resources;

class PostResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'author' => $this->author->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'slug' => $this->slug,
            'thumbnail' => $this->getThumbnailUrl(),
            'views' => $this->views,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'status' => $this->status,
            'tags' => $this->tags->pluck('name'),
        ];
    }

    private function getThumbnailUrl()
    {
        if ($this->thumbnail && str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }

        return $this->thumbnail ? asset($this->thumbnail) : null;
    }
}
