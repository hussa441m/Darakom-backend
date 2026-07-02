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
        ];
        Role::insert($roles);

        $profileProjectTypes = [
            ['role_id' => 1 ,'project_type_id' => 1],
            ['role_id' => 2,'project_type_id' => 2],
            ['role_id' => 3,'project_type_id' => 3],
            ['role_id' => 4 ,'project_type_id' => 4],
            ['role_id' => 5,'project_type_id' =>5],
        ];
        foreach ($profileProjectTypes as $profileProjectType ){
            Role::find($profileProjectType['role_id'])->projectTypes()->attach($profileProjectType['project_type_id']);
        }

        $users = [
            
            ['id' => 1, 'name' => 'رفعت الصاق', 'email' => 'builder@test.com', 'password' => Hash::make('123456'), 'type' => 'client', 'status' => 'pending'],
            ['id' => 2, 'name' => 'منير الأشرف ', 'email' => 'arct@test.com', 'password' => Hash::make('123456'), 'type' => 'client', 'status' => 'pending'],
            ['id' => 3, 'name' => 'هاني السعيد', 'email' => 'civil@test.com', 'password' => Hash::make('123456'), 'type' => 'client', 'status' => 'pending'],
            ['id' => 4, 'name' => 'فالح الشاطر', 'email' => 'exper@test.com', 'password' => Hash::make('123456'), 'type' => 'client', 'status' => 'active'],
            ['id' => 5, 'name' => 'راقي الصافي', 'email' => 'office@test.com', 'password' => Hash::make('123456'), 'type' => 'client', 'status' => 'pending'],

            ['id' => 6, 'name' => 'المشرف', 'email' => 'admin@test.com', 'password' => Hash::make('123456'), 'type' => 'admin', 'status' => 'active'],

            ['id' => 7, 'name' => 'وداد الفهيم', 'email' => 'cust@test.com', 'password' => Hash::make('123456'), 'type' => 'customer', 'status' => 'active'],
        ];
        User::insert($users);

        

        $profiles = [
            [ 'experience_start' => '2000-01-01', 'user_id' => 1, 'role_id' => 1],
            [ 'experience_start' => '2005-01-01', 'user_id' => 2, 'role_id' => 2],
            [ 'experience_start' => '2010-01-01', 'user_id' => 3, 'role_id' => 3],
            [ 'experience_start' => '2015-01-01', 'user_id' => 4, 'role_id' => 4],
            [ 'experience_start' => '2020-01-01', 'user_id' => 5, 'role_id' => 5],
        ];
        Profile::insert($profiles);

        $projects = [
            [
                'id' =>1 ,
                'start_date' => '2026-01-01',
                'duration' => 3,
                'area' =>  1000,
                'location_details' => 'Mazeh',
                'description' => 'ترميم منافع',
                'building_no' => '123',
                'status' => 'new',
                'comment' => '',
                'project_type_id' => 1,
                'province_id' => 2,
                'customer_id' => 7,
                'performed_by' => null
            ]
        ];
        Project::insert($projects);
        

        $offer = [
            'cost' => 50000,
            'duration' => 2,
            'details' => '',
            'project_id' => '1',
            'offered_by' => '4',
        ];
        Offer::create($offer);

        
        $contactTypes = [
            ['name' => 'phone'],
            ['name' => 'whatsApp'],
            ['name' => 'Telegram'],
        ];
        ContactType::insert($contactTypes);
    }
}
