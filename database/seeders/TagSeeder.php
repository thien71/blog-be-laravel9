<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = ['Life', 'Story', 'Journey ', 'Experiences', 'Moments', 'Mind'];

        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}
