<?php

namespace Database\Seeders;

use App\Models\Complaint;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $complaints = [

            [
                'id' => 1,
                'text' => 'تأخر مزود الخدمة عن الموعد المتفق عليه لبدء تنفيذ المشروع.',
                'type' => 'against_provider',
                'status' => 'pending',
                'admin_response' => null,
                'project_id' => 1,
                'user_id' => 7,
                'against_user_id' => 1,
            ],

            [
                'id' => 2,
                'text' => 'وجود مشاكل في جودة الأعمال المنفذة وعدم الالتزام بالمواصفات المتفق عليها.',
                'type' => 'against_provider',
                'status' => 'under_review',
                'admin_response' => null,
                'project_id' => 2,
                'user_id' => 7,
                'against_user_id' => 3,
            ],

            [
                'id' => 3,
                'text' => 'عدم التزام العميل بالدفعة المتفق عليها ضمن العرض.',
                'type' => 'against_client',
                'status' => 'resolved',
                'admin_response' => 'تم التواصل مع الطرفين وحل المشكلة.',
                'project_id' => 3,
                'user_id' => 1,
                'against_user_id' => 7,
            ],

            [
                'id' => 4,
                'text' => 'تم تقديم شكوى بخصوص تفاصيل المشروع.',
                'type' => 'against_me',
                'status' => 'rejected',
                'admin_response' => 'بعد مراجعة الشكوى تبين عدم وجود مخالفة.',
                'project_id' => 1,
                'user_id' => 1,
                'against_user_id' => 7,
            ],

        
            [
                'id' => 5,
                'text' => 'شكوى بخصوص تأخر التواصل مع مزود الخدمة.',
                'type' => 'against_provider',
                'status' => 'closed',
                'admin_response' => 'تمت معالجة المشكلة وإغلاق الشكوى.',
                'project_id' => 1,
                'user_id' => 7,
                'against_user_id' => 1,
            ],

        ];

        Complaint::insert($complaints);
    }
}