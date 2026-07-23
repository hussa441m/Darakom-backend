<?php

namespace Database\Seeders;

use App\Models\ContactType;
use App\Models\Offer;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectAndUserAndOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectTypes = [
            ['id' => 1,'name' => 'تنفيذ ' ],
            ['id' => 2,'name' => 'تصميم معماري' ],
            ['id' => 3,'name' => 'إشراف' ],
            ['id' => 4,'name' => 'استشارة ' ],
            ['id' => 5,'name' => 'تسليم مشروع كامل' ],
        ];
        ProjectType::insert($projectTypes);
        
        $roles = [
            ['id' => 1, 'name' => 'مقاول',],
            ['id' => 2, 'name' => 'مهندس معماري',],
            ['id' => 3, 'name' => 'مهندس مدني',],
            ['id' => 4, 'name' => 'مهندس مدني استشاري',],
            ['id' => 5, 'name' => 'المكاتب الهندسية',],
            ['id' => 6, 'name' => 'حرفي'],
        ];
        Role::insert($roles);

        $profileProjectTypes = [
            ['role_id' => 1 ,'project_type_id' => 1],
            ['role_id' => 2,'project_type_id' => 2],
            ['role_id' => 3,'project_type_id' => 3],
            ['role_id' => 4 ,'project_type_id' => 4],
            ['role_id' => 5,'project_type_id' =>5],
            ['role_id' => 6,'project_type_id' => 1],
        ];
        foreach ($profileProjectTypes as $profileProjectType ){
            Role::find($profileProjectType['role_id'])->projectTypes()->attach($profileProjectType['project_type_id']);
        }

         $users = [

           ['id' => 1,'first_name' => 'رفعت','last_name' => 'الصاق','email' => 'builder@test.com','password' => Hash::make('123456'),'type' => 'provider', 'status' => 'active',],
           ['id' => 2, 'first_name' => 'منير','last_name' => 'الأشرف','email' => 'arct@test.com','password' => Hash::make('123456'), 'type' => 'provider','status' => 'active',],
           ['id' => 3,'first_name' => 'هاني', 'last_name' => 'السعيد','email' => 'civil@test.com','password' => Hash::make('123456'),'type' => 'provider', 'status' => 'active',],
           ['id' => 4,'first_name' => 'فالح', 'last_name' => 'الشاطر', 'email' => 'exper@test.com','password' => Hash::make('123456'), 'type' => 'provider','status' => 'active',],
           ['id' => 5,'first_name' => 'راقي','last_name' => 'الصافي','email' => 'office@test.com','password' => Hash::make('123456'),'type' => 'provider','status' => 'active',],
           ['id' => 6,'first_name' => 'المشرف','last_name' => 'الإداري','email' => 'admin@test.com','password' => Hash::make('123456'),'type' => 'admin','status' => 'active',],
           ['id' => 7,'first_name' => 'وداد','last_name' => 'الفهيم','email' => 'client@test.com','password' => Hash::make('123456'),'type' => 'client','status' => 'active',],
           ['id' => 8,'first_name' => 'أحمد','last_name' => 'الحسن','email' => 'artisan@test.com','password' => Hash::make('123456'),'type' => 'provider','status' => 'active',],

        ];

         User::insert($users);

        
   
          $profiles = [

           ['experience_start' => '2000-01-01','work_area' => 'دمشق','bio' => 'مقاول متخصص في تنفيذ المشاريع السكنية.','experience_years' => 25,'user_id' => 1,'role_id' => 1,],
           ['experience_start' => '2005-01-01','work_area' => 'حلب','bio' => 'مهندس معماري متخصص في التصميم المعماري.','experience_years' => 20,'user_id' => 2,'role_id' => 2,],
           ['experience_start' => '2010-01-01','work_area' => 'حمص','bio' => 'مهندس مدني مختص بالإشراف الإنشائي.','experience_years' => 15,'user_id' => 3,'role_id' => 3,],
           ['experience_start' => '2015-01-01','work_area' => 'اللاذقية','bio' => 'مهندس مدني استشاري يقدم الاستشارات الهندسية.','experience_years' => 10,'user_id' => 4,'role_id' => 4,],
           ['experience_start' => '2020-01-01','work_area' => 'دمشق','bio' => 'مكتب هندسي يقدم حلولاً متكاملة للمشاريع.','experience_years' => 5,'user_id' => 5,'role_id' => 5,],
           ['experience_start' => '2018-01-01','work_area' => 'ريف دمشق','bio' => 'حرفي متخصص في أعمال الكهرباء والسباكة.','experience_years' => 7,'user_id' => 8,'role_id' => 6,],

        ];

         Profile::insert($profiles);
 
          $projects = [
           [
             'id' => 1,
             'project_code' => 'PRJ-0001',
  
             'title' => 'بناء منزل سكني',

             'work_type' => 'construction',
             'craftsman_type' => null,
             'tender_type' => 'normal',

             'start_date' => '2026-01-01',
             'end_date' => '2026-04-01',

             'area' => 1000,

             'location_details' => 'المزة',
             'building_no' => '123',
 
             'description' => 'إنشاء منزل سكني مؤلف من طابقين.',

             'visibility' => 'public',

             'provider_profile_id' => null,

             'tender_duration' => 3,
             'tender_duration_unit' => 'day',

             'budget' => 50000000,

             'status' => 'new',
             'execution_status' => 'not_started',
  
             'comment' => null,

             'province_id' => 2,
             'project_type_id' => 1,
             'client_id' => 7,
             'performed_by' => null,
            ]
        ];

        Project::insert($projects);
        

         $offer = [
             'cost' => 50000,
             'duration' => 2,
             'duration_unit' => 'month',
             'provider_comment' => 'نقدم عرضاً لتنفيذ المشروع بأفضل جودة وفي الوقت المحدد.',
             'details' => 'يشمل العرض جميع أعمال التنفيذ والمواد المطلوبة.',
             'project_id' => 1,
             'offered_by' => 4,
             'status' => 'pending',
             'reject_reason' => null,
        ];

         Offer::create($offer);

        
        $contactTypes = [
            ['id' => 1, 'name' => 'phone'],
            ['id' => 2, 'name' => 'whatsapp'],
            ['id' => 3, 'name' => 'telegram'],
            ['id' => 4, 'name' => 'email'],
        ];

         ContactType::insert($contactTypes);
        }
}