<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void
{
    $this->call(ProvinceSeeder::class);
        $this->call(SettingSeeder::class);

    $this->call(ProjectAndUserAndOfferSeeder::class);

    $this->call(ServiceCategorySeeder::class);
        $this->call(ServiceSeeder::class);

        $this->call(DocumentSeeder::class);
    $this->call(ArtisanServiceSeeder::class);

    $this->call(PreviousWorkSeeder::class);
    $this->call(ProjectInvitationSeeder::class);

    $this->call(RatingSeeder::class);
    $this->call(ComplaintSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(FavoriteSeeder::class);
        $this->call(ProjectReportSeeder::class);
    $this->call(NotificationSeeder::class);
    $this->call(StepSeeder::class);
    $this->call(QualificationSeeder::class);
    $this->call(ContactSeeder::class);

}
}
