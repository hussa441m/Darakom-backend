<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Step;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Project::all() as $project) {
            Step::create([
                'title' => 'الخطوة الأولى',
                'description' => 'الخطوة الأولى من المشروع مكتملة.',
                'date' => now()->toDateString(),
                'progress_percent' => 100,
                'status' => 'completed',
                'project_id' => $project->id,
            ]);

            Step::create([
                'title' => 'الخطوة الثانية',
                'description' => 'الخطوة الثانية من المشروع قيد التنفيذ.',
                'date' => now()->addDays(7)->toDateString(),
                'progress_percent' => 50,
                'status' => 'in_progress',
                'project_id' => $project->id,
            ]);

            Step::create([
                'title' => 'الخطوة الثالثة',
                'description' => 'الخطوة الثالثة من المشروع قيد الانتظار.',
                'date' => now()->addDays(14)->toDateString(),
                'progress_percent' => 0,
                'status' => 'not_started',
                'project_id' => $project->id,
            ]);
        }
    }
}
