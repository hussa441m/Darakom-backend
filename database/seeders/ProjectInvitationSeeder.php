<?php

namespace Database\Seeders;

use App\Models\ProjectInvitation;
use Illuminate\Database\Seeder;

class ProjectInvitationSeeder extends Seeder
{
    public function run(): void
    {
        $invitations = [

            // دعوة خاصة للمقاول Profile رقم 1
            [
                'project_id' => 2,
                'provider_profile_id' => 1,
                'status' => 'pending',
                'expires_at' => now()->addHours(48),
            ],

            // دعوة خاصة للمهندس المعماري Profile رقم 2
            [
                'project_id' => 3,
                'provider_profile_id' => 2,
                'status' => 'pending',
                'expires_at' => now()->addHours(24),
            ],

            // دعوة تم قبولها
            [
                'project_id' => 4,
                'provider_profile_id' => 3,
                'status' => 'accepted',
                'expires_at' => now()->subDays(2),
            ],

        ];

        foreach ($invitations as $invitation) {
            ProjectInvitation::create($invitation);
        }
    }
}