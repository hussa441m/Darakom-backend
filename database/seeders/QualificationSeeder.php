<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Qualification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Profile::all() as $profile) {
            Qualification::create([
                'name' => 'شهادة خبرة معتمدة',
                'image' => 'qualifications/dummy.jpg',
                'profile_id' => $profile->id,
            ]);

            Qualification::create([
                'name' => 'دورة تدريبية تخصصية',
                'image' => 'qualifications/dummy.jpg',
                'profile_id' => $profile->id,
            ]);
        }
    }
}
