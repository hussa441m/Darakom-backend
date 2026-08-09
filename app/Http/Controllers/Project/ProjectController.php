<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Profile;
use App\Notifications\UrgentProject;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
{
    $projects = Project::with([
        'projectType',
        'province',
        'client'
    ])
    ->latest()
    ->get();

    return apiSuccess( "جميع المشاريع",$projects);
}

public function show(Project $project)
{
    $project->load([
        'projectType',
        'province',
        'client',
        'offers',
        'documents',
        'reports',
    ]);

    return apiSuccess( "بيانات المشروع",$project);
}


   public function store(Request $request)
{
    
    $this->authorize('create', Project::class);
    $validated = $request->validate([
        'title' => 'required|string|max:100',
        'project_type_id' => 'required|exists:project_types,id',
        'province_id' => 'required|exists:provinces,id',

        'work_type' => 'required|in:construction,finishing',
        'tender_type' => 'required|in:urgent,normal',

        'visibility' => 'required|in:public,private',
        'invitation_type' => 'required|in:public,private',

        'location_details' => 'required|string',
        'building_no' => 'required|string|max:15',
        'description' => 'required|string',

        'area' => 'required|integer|min:1',
        'budget' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',

        'tender_duration' => 'required|integer|min:1',
        'tender_duration_unit' => 'required|in:hour,day',

        'craftsman_type' => 'nullable|in:electricity,plumbing,tiling,ac,gypsum,solar_energy,painting',
    ]);

    $validated['client_id'] = $request->user()->id;

    $validated['project_code'] = 'PRJ-' . str_pad(
        Project::max('id') + 1,
        4,
        '0',
        STR_PAD_LEFT
    );

    $project = Project::create($validated);

// إذا كان المشروع مستعجلاً وعاماً
if (
    $project->tender_type === 'urgent' &&
    $project->visibility === 'public' &&
    $project->invitation_type === 'public'
) {
    $profiles = Profile::whereHas('role.projectTypes', function ($query) use ($project) {
        $query->where('project_types.id', $project->project_type_id);
    })
    ->with('user')
    ->get();

    foreach ($profiles as $profile) {
        if ($profile->user) {
            $profile->user->notify(
                new UrgentProject($project->id)
            );
        }
    }
}

$project->load([
    'projectType',
    'province',
    'client'
]);

    return apiSuccess( "تم إنشاء المشروع بنجاح", $project);
}
   

    public function update(Request $request, Project $project)
{
    $this->authorize('update', $project);
    if ($project->provider_profile_id) {
    return apiError('لا يمكن تعديل المشروع بعد قبول أحد العروض.');
}

    $validated = $request->validate([
        'title' => 'sometimes|string|max:100',
        'project_type_id' => 'sometimes|exists:project_types,id',
        'province_id' => 'sometimes|exists:provinces,id',

        'work_type' => 'sometimes|in:construction,finishing',
        'tender_type' => 'sometimes|in:urgent,normal',

        'visibility' => 'sometimes|in:public,private',
        'invitation_type' => 'sometimes|in:public,private',

        'location_details' => 'sometimes|string',
        'building_no' => 'sometimes|string|max:15',
        'description' => 'sometimes|string',

        'area' => 'sometimes|integer|min:1',
        'budget' => 'nullable|numeric|min:0',

        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',

        'tender_duration' => 'sometimes|integer|min:1',
        'tender_duration_unit' => 'sometimes|in:hour,day',

        'craftsman_type' => 'nullable|in:electricity,plumbing,tiling,ac,gypsum,solar_energy,painting',
    ]);

    $project->update($validated);

    return apiSuccess( "تم تعديل المشروع بنجاح", $project->fresh()
);
}

    public function destroy(Project $project)
{
    $this->authorize('delete', $project);

    if ($project->provider_profile_id) {
        return apiError('لا يمكن حذف المشروع بعد قبول أحد العروض.');
    }

    $project->delete();

    return apiSuccess('تم حذف المشروع بنجاح.');
}

  
}