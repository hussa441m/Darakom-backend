<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $ratings = [
            [
                'id' => 1,
                'rating' => 5,
                'comment' => 'خدمة ممتازة وتنفيذ سريع.',
                'user_id' => 7,
                'profile_id' => 1,
            ],
            [
                'id' => 2,
                'rating' => 4,
                'comment' => 'عمل جيد.',
                'user_id' => 7,
                'profile_id' => 6,
            ],
        ];

        Rating::insert($ratings);
    }
}