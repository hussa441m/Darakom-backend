<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceCategories = [
            [
                'id' => 1,
                'name' => 'electricity',
                'display_name' => 'كهرباء',
            ],
            [
                'id' => 2,
                'name' => 'plumbing',
                'display_name' => 'سباكة',
            ],
            [
                'id' => 3,
                'name' => 'tiling',
                'display_name' => 'بلاط',
            ],
            [
                'id' => 4,
                'name' => 'ac',
                'display_name' => 'تكييف',
            ],
            [
                'id' => 5,
                'name' => 'gypsum',
                'display_name' => 'جبس بورد',
            ],
            [
                'id' => 6,
                'name' => 'solar_energy',
                'display_name' => 'طاقة شمسية',
            ],
            [
                'id' => 7,
                'name' => 'painting',
                'display_name' => 'دهان',
            ],
        ];

        ServiceCategory::insert($serviceCategories);
    }
}