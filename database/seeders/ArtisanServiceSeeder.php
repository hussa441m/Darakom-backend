<?php

namespace Database\Seeders;

use App\Models\ArtisanService;
use Illuminate\Database\Seeder;

class ArtisanServiceSeeder extends Seeder
{
    public function run(): void
    {
        $artisanServices = [
            [
                'profile_id' => 6,
                'service_category_id' => 1,
            ],
            [
                'profile_id' => 6,
                'service_category_id' => 4,
            ],
        ];

        ArtisanService::insert($artisanServices);
    }
}