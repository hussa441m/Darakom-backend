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
                'profile_id' => 1,
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'profile_id' => 6,
            ],
        ];

        Favorite::insert($favorites);
    }
}