<?php

namespace App\Http\Resources;

class PostResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name
            ] : null,
            'author' => $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'avatar' => $this->author->avatar
            ] : null,
            'title' => $this->title,
            'summary' => $this->summary ?? '',
            'content' => $this->content ?? '',
            'slug' => $this->slug ?? '',
            'thumbnail' => $this->getThumbnailUrl(),
            'views' => $this->views ?? 0,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'status' => $this->status ?? 'draft',
            'tags' => $this->tags ? $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name
                ];
            }) : [],
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
