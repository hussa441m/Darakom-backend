<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\ProjectReport;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;


class ProviderController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $profile = $user->profile;

        if (!$profile) {
            return apiError("Provider profile not found.", 404);
        }

        $activeProjects = Project::where(
            'provider_profile_id',
            $profile->id
        )->where('execution_status', 'in_progress')->count();

        $completedProjects = Project::where(
            'provider_profile_id',
            $profile->id
        )->where('execution_status', 'finished')->count();

        $newTenders = ProjectInvitation::where(
            'provider_profile_id',
            $profile->id
        )->where('status', 'pending')
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        })->count();

        $averageRating = $user->receivedRatings()->avg('rate');

        $averageRating = $averageRating
            ? round($averageRating, 1)
            : 0;

        $privateInvitations = ProjectInvitation::with('project')
            ->where('provider_profile_id', $profile->id)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(5)
            ->get();

        $projectsInProgress = Project::where(
            'provider_profile_id',
            $profile->id
        )
        ->where('execution_status', 'in_progress')
        ->latest()
        ->take(5)
        ->get();

        return apiSuccess( "بيانات لوحة تحكم مزود الخدمة",
            [
                'statistics' => [
                    'completed_projects' => $completedProjects,
                    'active_projects' => $activeProjects,
                    'new_tenders' => $newTenders,
                    'average_rating' => $averageRating,
                ],

                'private_invitations' => $privateInvitations,

                'projects_in_progress' => $projectsInProgress,
            ]
        );
    }

    public function publicTenders(Request $request)
    {
        // تم إضافة تعريف الـ user للوصول إلى المحافظة
        $user = $request->user();
        $profile = $user->profile;

        $projectTypeIds = $profile->role->projectTypes->pluck('id');

        $tenders = Project::with(['projectType','province','client'])
            ->where('visibility', 'public')
            ->where('invitation_type', 'public')
            ->whereIn('status', ['new', 'pending'])
            ->whereNull('provider_profile_id')
            // 👇 التعديل الأول: الفلترة حسب المحافظة
            ->where('province_id', $user->province_id)
            // 👇 التعديل الثاني: الفلترة حسب تخصص المزود
            ->whereIn('project_type_id', $projectTypeIds)
            ->latest()
            ->get();

        return apiSuccess("المناقصات العامة", $tenders);
    }

    public function privateTenders(Request $request)
    {
             $profile = $request->user()->profile;

            $invitations = ProjectInvitation::with([
           'project.projectType',
           'project.province',
           'project.client',
          ])
          ->where('provider_profile_id', $profile->id)
          ->where('status', 'pending')
          ->where(function ($query) {
          $query->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
         })
        ->latest()
        ->get();

       return apiSuccess('المناقصات الخاصة', $invitations);
    }


      public function showTender($id)
    {
        $user = request()->user();

        $profile = $user->profile;

        if (!$profile) {
           return apiError("لا يوجد ملف شخصي لمزود الخدمة");
      }

         $publicTender = Project::with([
           'projectType',
           'province',
           'client',
        ])
    ->where('id', $id)
    ->where('visibility', 'public')
    ->where('invitation_type', 'public')
    ->whereIn('status', ['new', 'pending'])
    ->whereNull('provider_profile_id')
    // يمكن أيضاً إضافة الفلترة هنا لمنع المزود من الدخول لرابط مناقصة غير مناسبة له عبر الـ ID
    ->where('province_id', $user->province_id)
    ->whereIn('project_type_id', $profile->role->projectTypes->pluck('id'))
    ->first();

    if ($publicTender) {
        return apiSuccess(
            "تفاصيل المناقصة العامة",
            $publicTender
        );
    }

    $privateTender = ProjectInvitation::with([
        'project.projectType',
        'project.province',
        'project.client',
    ])
    ->where('project_id', $id)
    ->where('provider_profile_id', $profile->id)
    ->where('status', 'pending')
    ->where(function ($query) {
        $query->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
    })
    ->first();

    if ($privateTender) {
        return apiSuccess(
            "تفاصيل المناقصة الخاصة",
            $privateTender
        );
    }

    return apiError("هذه المناقصة غير متاحة لك");
}



    public function declineInvitation($id)
   {
       $user = request()->user();

       $profile = $user->profile;

        if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
       }

       $invitation = ProjectInvitation::where('id', $id)
         ->where('provider_profile_id', $profile->id)
         ->where('status', 'pending')
         ->first();

        if (!$invitation) {
          return apiError("الدعوة غير موجودة أو لا يمكنك رفضها");
        }

        $invitation->update([
          'status' => 'declined',
    ]);

        return apiSuccess("تم رفض الدعوة بنجاح",$invitation);
    }


public function projects(Request $request)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
    }

    $projects = Project::with([
        'projectType',
        'province',
        'client',
    ])
    ->where('provider_profile_id', $profile->id)
    ->latest()
    ->get();

    return apiSuccess("مشاريعي", $projects);
}

public function showProject(Request $request, Project $project)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
    }

    if ($project->provider_profile_id != $profile->id) {
        return apiError("هذا المشروع غير متاح لك");
    }

    $project->load([
        'projectType',
        'province',
        'client',
        'offers',
        'reports',
        'documents',
    ]);

    return apiSuccess("تفاصيل المشروع", $project);
}

  public function projectTracking(Request $request, Project $project)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
    }
    if ($project->provider_profile_id != $profile->id) {
        return apiError("هذا المشروع غير متاح لك");
    }

    $project->load([
        'projectType',
        'province',
        'client',
        'reports',
    ]);

    return apiSuccess("متابعة المشروع", $project);
}

  public function addReport(Request $request, Project $project)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
    }

    if ($project->provider_profile_id != $profile->id) {
        return apiError("هذا المشروع غير متاح لك");
    }

    $validated = $request->validate([
        'description' => 'required|string',
        'reported_progress' => 'nullable|integer|min:0|max:100',
        'step_id' => 'nullable|exists:steps,id',
    ]);

    $report = ProjectReport::create([
        'description' => $validated['description'],
        'reported_progress' => $validated['reported_progress'] ?? null,
        'project_id' => $project->id,
        'user_id' => $request->user()->id,
        'step_id' => $validated['step_id'] ?? null,
    ]);

    if (isset($validated['reported_progress'])) {
        $project->update([
            'progress_percentage' => $validated['reported_progress'],
        ]);
    }

    return apiSuccess("تمت إضافة التقرير بنجاح", $report);
}

public function endProject(Project $project)
{
    $profile = request()->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة");
    }

    if ($project->provider_profile_id != $profile->id) {
        return apiError("هذا المشروع لا يخصك");
    }

    if ($project->status == 'completed') {
        return apiError("المشروع منتهي مسبقاً");
    }

    $project->update([
        'status' => 'completed',
        'execution_status' => 'finished',
        'progress_percentage' => 100,
        'end_date' => now(),
    ]);

    return apiSuccess("تم إنهاء المشروع بنجاح", $project->fresh());
}
}
