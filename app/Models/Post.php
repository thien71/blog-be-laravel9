<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'category_id', 'title', 'summary', 'content', 'slug', 'thumbnail', 'views', 'status'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public static function generateUniqueSlug($title, $postId = null)
    {
        $slug = Str::slug($title);
        $count = self::where('slug', 'LIKE', "$slug%")
            ->when($postId, fn($query) => $query->where('id', '!=', $postId))
            ->count();

        return $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
    }
}
