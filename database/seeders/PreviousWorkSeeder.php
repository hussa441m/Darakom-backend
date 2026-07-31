<?php

namespace Database\Seeders;

use App\Models\PreviousWork;
use Illuminate\Database\Seeder;

class PreviousWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $previousWorks = [

            // Profile 1 - المقاول
            [
                'profile_id' => 1,
                'title' => 'تشطيب فيلا مودرن',
                'location' => 'دمشق، المزة',
                'date' => '2025-12-15',
                'description' => 'تنفيذ أعمال تشطيب متكاملة لفيلا مودرن تشمل الألمنيوم والدهانات والرخام والإضاءة الذكية.',
            ],

            [
                'profile_id' => 1,
                'title' => 'بناء ملحق خارجي',
                'location' => 'حلب، حي الفردوس',
                'date' => '2025-08-20',
                'description' => 'بناء ملحق خارجي متكامل مؤلف من غرفتين وصالة ومطبخ مع تنفيذ أعمال التشطيب.',
            ],

            // Profile 2 - المهندس المعماري
            [
                'profile_id' => 2,
                'title' => 'تصميم منزل سكني',
                'location' => 'حلب، الحمدانية',
                'date' => '2025-11-10',
                'description' => 'تصميم معماري متكامل لمنزل سكني مع إعداد المخططات المعمارية وتوزيع الفراغات.',
            ],

            [
                'profile_id' => 2,
                'title' => 'تصميم داخلي لشقة فاخرة',
                'location' => 'دمشق، مشروع دمر',
                'date' => '2025-07-05',
                'description' => 'تصميم داخلي لشقة سكنية فاخرة يشمل توزيع الأثاث والإضاءة واختيار الخامات.',
            ],

            // Profile 3 - المهندس المدني
            [
                'profile_id' => 3,
                'title' => 'إشراف على بناء منزل',
                'location' => 'حمص، الإنشاءات',
                'date' => '2025-10-01',
                'description' => 'الإشراف الهندسي على تنفيذ الأعمال الإنشائية ومتابعة مراحل التنفيذ وفق المخططات.',
            ],

            [
                'profile_id' => 3,
                'title' => 'إشراف على مبنى سكني',
                'location' => 'حمص، حي الزهراء',
                'date' => '2025-06-15',
                'description' => 'الإشراف على تنفيذ مبنى سكني ومتابعة الجودة والمواصفات الفنية.',
            ],

            // Profile 4 - المهندس الاستشاري
            [
                'profile_id' => 4,
                'title' => 'استشارة هندسية لمشروع سكني',
                'location' => 'اللاذقية، الكورنيش',
                'date' => '2025-09-12',
                'description' => 'تقديم استشارات هندسية ودراسة الحلول المناسبة لتنفيذ المشروع السكني.',
            ],

            // Profile 5 - المكتب الهندسي
            [
                'profile_id' => 5,
                'title' => 'تصميم وإدارة مشروع تجاري',
                'location' => 'دمشق، المالكي',
                'date' => '2025-05-20',
                'description' => 'تصميم وإدارة مشروع تجاري متكامل بدءاً من إعداد المخططات وحتى متابعة التنفيذ.',
            ],

        ];

        foreach ($previousWorks as $work) {
            PreviousWork::create($work);
        }
    }
}