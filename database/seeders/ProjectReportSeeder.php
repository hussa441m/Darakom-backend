<?php

namespace Database\Seeders;

use App\Models\ProjectReport;
use Illuminate\Database\Seeder;

class ProjectReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'id' => 1,
                'project_id' => 1,
                'reported_by' => 7,
                'reason' => 'معلومات غير صحيحة',
                'details' => 'يوجد معلومات تحتاج للمراجعة.',
                'status' => 'pending',
            ],
        ];

        ProjectReport::insert($reports);
    }
}