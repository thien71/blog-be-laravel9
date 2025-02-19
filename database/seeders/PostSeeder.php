<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $authors = User::where('role', 'author')->get();

        foreach ($authors as $author) {
            for ($i = 1; $i <= 10; $i++) {
                $post = Post::create([
                    'user_id' => $author->id,
                    'title' => "Bài viết mẫu $i của " . $author->name,
                    'summary' => "Tóm tắt của bài $i",
                    'content' => 'Đây là nội dung bài viết mẫu số ' . $i,
                    'slug' => Str::slug("Bài viết mẫu $i của " . $author->name . '-' . Str::random(5)),
                    'thumbnail' => 'https://placehold.co/150',
                    'views' => rand(0, 500),
                    'status' => rand(0, 1) ? 'published' : 'draft',
                ]);

                $tags = Tag::inRandomOrder()->limit(rand(1, 3))->pluck('id');
                $post->tags()->attach($tags);
            }
        }
    }
}
