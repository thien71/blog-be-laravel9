<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'avatar' => 'https://placehold.co/150',
        ]);

        User::create([
            'name' => 'Author User',
            'email' => 'author@example.com',
            'password' => bcrypt('password'),
            'role' => 'author',
            'avatar' => 'https://placehold.co/150',
        ]);
    }
}
