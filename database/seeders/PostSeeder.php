<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'id' => 1,
                'description' => 'مشروع إنشاء منزل سكني بمواصفات حديثة.',
                'profile_id' => 1,
            ],
            [
                'id' => 2,
                'description' => 'تقديم خدمات حرفية متنوعة للمنازل.',
                'profile_id' => 6,
            ],
        ];

        Post::insert($posts);
    }
}