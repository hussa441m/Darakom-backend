<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [

            [
                'id' => (string) Str::uuid(),
                'type' => 'new_offer',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 7,
                'data' => json_encode([
                    'title' => 'عرض جديد',
                    'message' => 'تم استلام عرض جديد على مشروعك.',
                    'project_id' => 1,
                    'offer_id' => 1,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => (string) Str::uuid(),
                'type' => 'project_invitation',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 2,
                'data' => json_encode([
                    'title' => 'دعوة لمشروع',
                    'message' => 'تمت دعوتك للمشاركة في مشروع خاص.',
                    'project_id' => 2,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => (string) Str::uuid(),
                'type' => 'offer_accepted',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 1,
                'data' => json_encode([
                    'title' => 'تم قبول العرض',
                    'message' => 'تم قبول عرضك من قبل العميل.',
                    'project_id' => 3,
                    'offer_id' => 3,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

          
            [
                'id' => (string) Str::uuid(),
                'type' => 'project_status_updated',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 7,
                'data' => json_encode([
                    'title' => 'تحديث حالة المشروع',
                    'message' => 'تم تحديث حالة مشروعك.',
                    'project_id' => 4,
                    'status' => 'in_progress',
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        
            [
                'id' => (string) Str::uuid(),
                'type' => 'new_rating',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 1,
                'data' => json_encode([
                    'title' => 'تقييم جديد',
                    'message' => 'حصلت على تقييم جديد من العميل.',
                    'project_id' => 1,
                    'rating' => 5,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => (string) Str::uuid(),
                'type' => 'new_complaint',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 6,
                'data' => json_encode([
                    'title' => 'شكوى جديدة',
                    'message' => 'تم تقديم شكوى جديدة وتحتاج إلى المراجعة.',
                    'complaint_id' => 1,
                    'project_id' => 1,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ==========================================
            // 7. إشعار مقروء
            // ==========================================
            [
                'id' => (string) Str::uuid(),
                'type' => 'offer_rejected',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => 3,
                'data' => json_encode([
                    'title' => 'تم رفض العرض',
                    'message' => 'تم رفض عرضك من قبل العميل.',
                    'project_id' => 2,
                    'offer_id' => 2,
                ]),
                'read_at' => now()->subHours(5),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subHours(5),
            ],
        ];

        DB::table('notifications')->insert($notifications);
    }
}