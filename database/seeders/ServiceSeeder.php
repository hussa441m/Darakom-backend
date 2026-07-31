<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [

            ['id' => 1, 'name' => 'تصميم معماري'],
            ['id' => 2, 'name' => 'تصميم إنشائي'],
            ['id' => 3, 'name' => 'إشراف هندسي'],
            ['id' => 4, 'name' => 'إدارة مشاريع'],
            ['id' => 5, 'name' => 'استشارات هندسية'],
            ['id' => 6, 'name' => 'تنفيذ مشاريع'],
            ['id' => 7, 'name' => 'تشطيبات'],
            ['id' => 8, 'name' => 'تصميم داخلي'],
            ['id' => 9, 'name' => 'حصر كميات'],
            ['id' => 10, 'name' => 'رفع مساحي'],

        ];

        Service::insert($services);

        /*
        |--------------------------------------------------------------------------
        | ربط الخدمات مع مزودي الخدمة
        |--------------------------------------------------------------------------
        */

        // المقاول
        Profile::find(1)?->services()->attach([
            6, // تنفيذ مشاريع
            7, // تشطيبات
            9, // حصر كميات
        ]);

        // المهندس المعماري
        Profile::find(2)?->services()->attach([
            1, // تصميم معماري
            3, // إشراف هندسي
            8, // تصميم داخلي
        ]);

        // المهندس المدني
        Profile::find(3)?->services()->attach([
            2, // تصميم إنشائي
            3, // إشراف هندسي
            9, // حصر كميات
        ]);

        // المهندس الاستشاري
        Profile::find(4)?->services()->attach([
            5, // استشارات هندسية
            4, // إدارة مشاريع
            3, // إشراف هندسي
        ]);

        // المكتب الهندسي
        Profile::find(5)?->services()->attach([
            1,
            2,
            3,
            4,
            5,
            8,
        ]);

        // الحرفي
        // لا نربطه بهذا الجدول لأنه يستخدم service_categories و artisan_services
    }
}