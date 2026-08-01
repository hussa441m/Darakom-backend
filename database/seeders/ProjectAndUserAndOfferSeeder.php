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

    [
        'id' => 1,
        'first_name' => 'رفعت',
        'last_name' => 'الصاق',
        'email' => 'builder@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 1,
    ],

    [
        'id' => 2,
        'first_name' => 'منير',
        'last_name' => 'الأشرف',
        'email' => 'arct@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 2,
    ],

    [
        'id' => 3,
        'first_name' => 'هاني',
        'last_name' => 'السعيد',
        'email' => 'civil@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 3,
    ],

    [
        'id' => 4,
        'first_name' => 'فالح',
        'last_name' => 'الشاطر',
        'email' => 'exper@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 4,
    ],

    [
        'id' => 5,
        'first_name' => 'راقي',
        'last_name' => 'الصافي',
        'email' => 'office@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 5,
    ],

    [
        'id' => 6,
        'first_name' => 'المشرف',
        'last_name' => 'الإداري',
        'email' => 'admin@test.com',
        'password' => Hash::make('123456'),
        'type' => 'admin',
        'status' => 'active',
        'province_id' => 1,
    ],

    [
        'id' => 7,
        'first_name' => 'وداد',
        'last_name' => 'الفهيم',
        'email' => 'client@test.com',
        'password' => Hash::make('123456'),
        'type' => 'client',
        'status' => 'active',
        'province_id' => 2,
    ],

    [
        'id' => 8,
        'first_name' => 'أحمد',
        'last_name' => 'الحسن',
        'email' => 'artisan@test.com',
        'password' => Hash::make('123456'),
        'type' => 'provider',
        'status' => 'active',
        'province_id' => 6,
    ],

];

         User::insert($users);

        
   
         $profiles = [

    [
        'experience_start' => '2000-01-01',
        'experience_years' => 25,
        'work_area' => 'دمشق',
        'bio' => 'مقاول متخصص في تنفيذ المشاريع السكنية.',
        'syndicate_number' => 'SY-1001',
        'logo' => null,
        'admin_comment' => null,
        //'province_id' => 1,
        'user_id' => 1,
        'role_id' => 1,
    ],

    [
        'experience_start' => '2005-01-01',
        'experience_years' => 20,
        'work_area' => 'حلب',
        'bio' => 'مهندس معماري متخصص في التصميم المعماري.',
        'syndicate_number' => 'SY-1002',
        'logo' => null,
        'admin_comment' => null,
        //'province_id' => 2,
        'user_id' => 2,
        'role_id' => 2,
    ],

    [
        'experience_start' => '2010-01-01',
        'experience_years' => 15,
        'work_area' => 'حمص',
        'bio' => 'مهندس مدني مختص بالإشراف الإنشائي.',
        'syndicate_number' => 'SY-1003',
        'logo' => null,
        'admin_comment' => null,
       // 'province_id' => 3,
        'user_id' => 3,
        'role_id' => 3,
    ],

    [
        'experience_start' => '2015-01-01',
        'experience_years' => 10,
        'work_area' => 'اللاذقية',
        'bio' => 'مهندس مدني استشاري يقدم الاستشارات الهندسية.',
        'syndicate_number' => 'SY-1004',
        'logo' => null,
        'admin_comment' => null,
      //  'province_id' => 4,
        'user_id' => 4,
        'role_id' => 4,
    ],

    [
        'experience_start' => '2020-01-01',
        'experience_years' => 5,
        'work_area' => 'دمشق',
        'bio' => 'مكتب هندسي يقدم حلولاً متكاملة للمشاريع.',
        'syndicate_number' => 'SY-1005',
        'logo' => null,
        'admin_comment' => null,
      //  'province_id' => 5,
        'user_id' => 5,
        'role_id' => 5,
    ],

    [
        'experience_start' => '2018-01-01',
        'experience_years' => 7,
        'work_area' => 'ريف دمشق',
        'bio' => 'حرفي متخصص في أعمال الكهرباء والسباكة.',
        'syndicate_number' => 'SY-1006',
        'logo' => null,
        'admin_comment' => null,
      //  'province_id' => 6,
        'user_id' => 8,
        'role_id' => 6,
    ],

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
        'invitation_type' => 'public',

        'provider_profile_id' => 1,

        'tender_duration' => 3,
        'tender_duration_unit' => 'day',

        'budget' => 50000000,

        'status' => 'new',
        'execution_status' => 'not_started',
        'progress_percentage' => 0,

        'comment' => null,

        'province_id' => 2,
        'project_type_id' => 1,
        'client_id' => 7,
        'performed_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],



    [
        'id' => 2,
        'project_code' => 'PRJ-0002',

        'title' => 'تصميم داخلي وتشطيب شقة فاخرة',

        'work_type' => 'finishing',
        'craftsman_type' => null,
        'tender_type' => 'normal',

        'start_date' => null,
        'end_date' => null,

        'area' => 0,

        'location_details' => 'مشروع دمر',
        'building_no' => 'غير محدد',

        'description' => 'تصميم داخلي وتنفيذ أعمال تشطيب متكاملة لشقة فاخرة.',

        'visibility' => 'private',
        'invitation_type' => 'private',

        'provider_profile_id' => null,

        'tender_duration' => 48,
        'tender_duration_unit' => 'hour',

        'budget' => 8500000,

        'status' => 'new',
        'execution_status' => 'not_started',
        'progress_percentage' => 0,

        'comment' => null,

        'province_id' => 4,
        'project_type_id' => 2,
        'client_id' => 7,
        'performed_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],

    [
        'id' => 3,
        'project_code' => 'PRJ-0003',

        'title' => 'توريد وتركيب سيراميك ورخام',

        'work_type' => 'finishing',
        'craftsman_type' => null,
        'tender_type' => 'normal',

        'start_date' => null,
        'end_date' => null,

        'area' => 0,

        'location_details' => 'حي المحطة',
        'building_no' => 'غير محدد',

        'description' => 'توريد وتركيب أعمال السيراميك والرخام للمشروع.',

        'visibility' => 'private',
        'invitation_type' => 'private',

        'provider_profile_id' => null,

        'tender_duration' => 24,
        'tender_duration_unit' => 'hour',

        'budget' => 3200000,

        'status' => 'new',
        'execution_status' => 'not_started',
        'progress_percentage' => 0,

        'comment' => null,

        'province_id' => 3,
        'project_type_id' => 1,
        'client_id' => 7,
        'performed_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],

    [
        'id' => 4,
       'project_code' => 'PRJ-0004',

       'title' => 'ترميم وتجديد فيلا كلاسيكية',

       'work_type' => 'finishing',
       'craftsman_type' => null,
       'tender_type' => 'normal',

       'start_date' => '2026-05-05',
       'end_date' => null,

       'area' => 500,

       'location_details' => 'حي الخالدية',
       'building_no' => '456',

       'description' => 'ترميم وتجديد فيلا كلاسيكية بشكل كامل.',

       'visibility' => 'private',
       'invitation_type' => 'private',

      'provider_profile_id' => null,

      'tender_duration' => 48,
      'tender_duration_unit' => 'hour',

      'budget' => 10000000,

      'status' => 'new',
      'execution_status' => 'in_progress',
      'progress_percentage' => 60,

      'comment' => null,

      'province_id' => 1,
      'project_type_id' => 5,
      'client_id' => 7,
      'performed_by' => null,
      'created_at' => now(),
      'updated_at' => now(),
    ],
    [
    'id' => 5,
    'project_code' => 'PRJ-0005',

    'title' => 'بناء مسبح خارجي مع تنسيق الحدائق',

    'work_type' => 'construction',
    'craftsman_type' => null,
    'tender_type' => 'normal',

    'start_date' => null,
    'end_date' => null,

    'area' => 300,

    'location_details' => 'يعفور',
    'building_no' => '789',

    'description' => 'بناء مسبح خارجي مع تنفيذ أعمال تنسيق الحدائق المحيطة به.',

    'visibility' => 'private',
    'invitation_type' => 'private',

    'provider_profile_id' => null,

    'tender_duration' => 72,
    'tender_duration_unit' => 'hour',

    'budget' => 15000000,

    'status' => 'new',
    'execution_status' => 'not_started',
    'progress_percentage' => 0,

    'comment' => null,

    'province_id' => 1,
    'project_type_id' => 1,
    'client_id' => 7,
    'performed_by' => null,
    'created_at' => now(),
    'updated_at' => now(),
],
];

    Project::insert($projects);
        

         $offers=[

     [
     'cost'=>45000,
     'duration'=>30,
     'duration_unit'=>'day',
     'provider_comment'=>'أفضل سعر.',
    'details'=>'تنفيذ كامل.',
    'project_id'=>1,
    'offered_by'=>1,
    'status'=>'pending'
   ],

   [
    'cost'=>120000,
    'duration'=>45,
    'duration_unit'=>'day',
    'provider_comment'=>'يشمل جميع المواد.',
    'details'=>'عرض كامل.',
    'project_id'=>2,
    'offered_by'=>3,
    'status'=>'rejected',
    'reject_reason'=>'السعر مرتفع'
   ],

   [
    'cost'=>85000,
    'duration'=>60,
    'duration_unit'=>'day',
    'provider_comment'=>'تشطيب فاخر.',
    'details'=>'أفضل المواد.',
    'project_id'=>3,
    'offered_by'=>1,
    'status'=>'pending'
   ],

  ];

    foreach($offers as $offer){

    Offer::create($offer);

  }

        $contactTypes = [
            ['id' => 1, 'name' => 'phone'],
            ['id' => 2, 'name' => 'whatsapp'],
            ['id' => 3, 'name' => 'telegram'],
            ['id' => 4, 'name' => 'email'],
        ];

         ContactType::insert($contactTypes);
        }
}