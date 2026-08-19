<?php

namespace Database\Seeders;

use App\Models\ContactType;
use App\Models\Offer;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ProjectAndUserAndOfferSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        // إعداد Faker لدعم اللغة العربية
        $faker = Faker::create('ar_SA'); 

        // 1. إعداد الأنواع والأدوار الأساسية
        ProjectType::insertOrIgnore([
            ['id' => 1, 'name' => 'تنفيذ'], ['id' => 2, 'name' => 'تصميم معماري'],
            ['id' => 3, 'name' => 'إشراف'], ['id' => 4, 'name' => 'استشارة'],
            ['id' => 5, 'name' => 'تسليم مشروع كامل'],
        ]);
        
        Role::insertOrIgnore([
            ['id' => 1, 'name' => 'مقاول'], ['id' => 2, 'name' => 'مهندس معماري'],
            ['id' => 3, 'name' => 'مهندس مدني'], ['id' => 4, 'name' => 'مهندس مدني استشاري'],
            ['id' => 5, 'name' => 'المكاتب الهندسية'], ['id' => 6, 'name' => 'حرفي'],
        ]);
        
        foreach ([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 1] as $roleId => $projectTypeId) {
            Role::findOrFail($roleId)->projectTypes()->syncWithoutDetaching($projectTypeId);
        }

        // 2. إنشاء حساب الأدمن
        User::insertOrIgnore([
            'id' => 1,
            'first_name' => 'المشرف',
            'last_name' => 'الإداري',
            'email' => 'admin@test.com',
            'phone' => '0991234567',
            'address' => 'دمشق - مركز الإدارة',
            'password' => Hash::make('123456'),
            'type' => 'admin',
            'status' => 'active',
            'province_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. إنشاء 15 عميل (ببيانات عشوائية حقيقية)
        $clients = [];
        for ($i = 1; $i <= 15; $i++) {
            $clients[] = User::create([
                'first_name' => $faker->firstName, 
                'last_name' => $faker->lastName,
                'email' => 'client' . $i . '@test.com', 
                'phone' => '09' . $faker->randomNumber(8, true), // توليد رقم يبدأ بـ 09
                'address' => $faker->city . ' - ' . $faker->streetName, // مدينة وشارع عشوائي
                'password' => Hash::make('123456'),
                'type' => 'client', 
                'status' => 'active', 
                'province_id' => rand(1, 6),
            ]);
        }

        // 4. إنشاء 25 مزود خدمة (ببيانات عشوائية حقيقية)
        $providers = [];
        $providerStatuses = ['active', 'pending', 'banned'];
        for ($i = 1; $i <= 25; $i++) {
            $user = User::create([
                'first_name' => $faker->firstName, 
                'last_name' => $faker->lastName,
                'email' => 'provider' . $i . '@test.com', 
                'phone' => '09' . $faker->randomNumber(8, true),
                'address' => $faker->city . ' - ' . $faker->streetAddress,
                'password' => Hash::make('123456'),
                'type' => 'provider', 
                'status' => $providerStatuses[array_rand($providerStatuses)],
                'province_id' => rand(1, 6),
            ]);
            
            $providers[] = Profile::create([
                'experience_years' => rand(1, 25), 
                'work_area' => 'محافظة ' . rand(1, 6),
                'bio' => $faker->realText(100), // نص عربي عشوائي للسيرة الذاتية
                'syndicate_number' => 'SY-' . $faker->unique()->randomNumber(4, true),
                'user_id' => $user->id, 
                'role_id' => rand(1, 6),
            ]);
        }

        // 5. إنشاء 50 مشروع متنوع (بنصوص عشوائية)
        $projects = [];
        $workTypes = ['construction', 'finishing'];
        $tenderTypes = ['urgent', 'normal'];
        $visibilities = ['public', 'private'];
        $projectStatuses = ['new', 'pending', 'completed', 'open', 'rejected'];
        $executionStatuses = ['not_started', 'in_progress', 'paused', 'finished'];
        
        for ($i = 1; $i <= 50; $i++) {
            $visibility = $visibilities[array_rand($visibilities)];
            $projects[] = Project::create([
                'project_code' => 'PRJ-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'title' => 'مشروع ' . $faker->catchPhrase, // عنوان عشوائي
                'work_type' => $workTypes[array_rand($workTypes)],
                'tender_type' => $tenderTypes[array_rand($tenderTypes)],
                'start_date' => $now->copy()->addDays($i),
                'end_date' => $now->copy()->addDays($i + rand(30, 180)),
                'area' => rand(50, 2500), 
                'location_details' => $faker->address, // عنوان تفصيلي عشوائي
                'building_no' => (string) $faker->buildingNumber,
                'description' => $faker->realText(200), // وصف مشروع تفصيلي
                'visibility' => $visibility, 
                'invitation_type' => $visibility,
                'tender_duration' => rand(1, 30), 
                'tender_duration_unit' => rand(0, 1) ? 'day' : 'hour',
                'budget' => rand(1000000, 50000000), 
                'status' => $projectStatuses[array_rand($projectStatuses)],
                'execution_status' => $executionStatuses[array_rand($executionStatuses)],
                'progress_percentage' => rand(0, 100), 
                'province_id' => rand(1, 6),
                'project_type_id' => rand(1, 5), 
                'client_id' => $clients[array_rand($clients)]->id,
                'created_at' => $now, 
                'updated_at' => $now,
            ]);
        }

        // 6. إضافة عروض أسعار للمشاريع
        $offerStatuses = ['pending', 'accepted', 'rejected'];
        $durationUnits = ['day', 'month', 'year'];
        foreach ($projects as $project) {
            foreach (collect($providers)->random(rand(2, 5)) as $provider) {
                $status = $offerStatuses[array_rand($offerStatuses)];
                Offer::create([
                    'cost' => rand(1000000, 50000000), 
                    'duration' => rand(1, 100),
                    'duration_unit' => $durationUnits[array_rand($durationUnits)],
                    'provider_comment' => $faker->realText(50), // تعليق عشوائي من المزود
                    'details' => $faker->realText(100), // تفاصيل عرض السعر
                    'project_id' => $project->id, 
                    'offered_by' => $provider->id, 
                    'status' => $status,
                    'reject_reason' => $status === 'rejected' ? $faker->realText(40) : null,
                    'start_date' => $now->copy()->addDays(rand(1, 30)),
                ]);
            }
        }

        // 7. أنواع قنوات الاتصال
        ContactType::insertOrIgnore([
            ['id' => 1, 'name' => 'phone'], ['id' => 2, 'name' => 'whatsapp'],
            ['id' => 3, 'name' => 'telegram'], ['id' => 4, 'name' => 'email'],
        ]);
    }
}