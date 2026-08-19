<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $favorites = [
            [
                'id' => 1,
                'user_id' => 7,
                'favorite_user_id' => 1,
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'favorite_user_id' => 8,
            ],
        ];

        Favorite::insert($favorites);
    }
}