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
                'title' => 'مشروع بناء منزل جديد',
                'description' => 'مشروع إنشاء منزل سكني بمواصفات حديثة.',
                'user_id' => 7,
            ],
            [
                'id' => 2,
                'title' => 'خدمات كهرباء وسباكة',
                'description' => 'تقديم خدمات حرفية متنوعة للمنازل.',
                'user_id' => 8,
            ],
        ];

        Post::insert($posts);
    }
}