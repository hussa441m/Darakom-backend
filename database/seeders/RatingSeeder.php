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
                'rate' => 5,
                'comment' => 'خدمة ممتازة وتنفيذ سريع.',
                'project_id' => 1,
                'user_id' => 7,
                'to_user_id' => 1,
            ],

            [
                'id' => 2,
                'rate' => 4,
                'comment' => 'عمل جيد.',
                'project_id' => 3,
                'user_id' => 7,
                'to_user_id' => 8,
            ],
        ];

        Rating::insert($ratings);
    }
}