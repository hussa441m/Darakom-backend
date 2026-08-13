<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Profile;
use App\Notifications\ProjectInvitationNotification;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
public function store(Request $request, Project $project)
{
    $this->authorize('update', $project);

    if ($project->visibility != 'private') {
        return apiError('لا يمكن إرسال دعوات لمشروع عام.');
    }

    if ($project->provider_profile_id) {
        return apiError('تم اختيار مزود الخدمة لهذا المشروع بالفعل.');
    }

    $validated = $request->validate([
        'provider_profile_id' => 'required|exists:profiles,id',
        'expires_at' => 'nullable|date|after:now',
    ]);

    $exists = ProjectInvitation::where('project_id', $project->id)
        ->where('provider_profile_id', $validated['provider_profile_id'])
        ->exists();

    if ($exists) {
        return apiError('تم إرسال دعوة لهذا المزود مسبقًا.');
    }

    $invitation = ProjectInvitation::create([
        'project_id' => $project->id,
        'provider_profile_id' => $validated['provider_profile_id'],
        'expires_at' => $validated['expires_at'] ?? null,
    ]);
    $profile = Profile::with('user')
    ->find($validated['provider_profile_id']);

if ($profile && $profile->user) {
    $profile->user->notify(
        new ProjectInvitation(
            $project->id,
            $invitation->id
        )
    );
}

    return apiSuccess('تم إرسال الدعوة بنجاح.', $invitation);
}

public function index(Request $request)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة.");
    }

    $invitations = ProjectInvitation::with([
        'project.projectType',
        'project.province',
        'project.client',
    ])
    ->where('provider_profile_id', $profile->id)
    ->latest()
    ->get();

    return apiSuccess( "الدعوات الخاصة بك",$invitations);
}

public function show(Request $request, ProjectInvitation $invitation)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة.");
    }

    if ($invitation->provider_profile_id != $profile->id) {
        return apiError("هذه الدعوة لا تخصك.");
    }

    $invitation->load([
        'project.projectType',
        'project.province',
        'project.client',
    ]);

    return apiSuccess("تفاصيل الدعوة",$invitation);
}

public function accept(Request $request, ProjectInvitation $invitation)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة.");
    }

    if ($invitation->provider_profile_id != $profile->id) {
        return apiError("هذه الدعوة لا تخصك.");
    }

    if ($invitation->status != 'pending') {
        return apiError("تم الرد على هذه الدعوة مسبقاً.");
    }

    if ($invitation->expires_at && now()->gt($invitation->expires_at)) {
        return apiError("انتهت صلاحية هذه الدعوة.");
    }

    $invitation->update([
        'status' => 'accepted',
        'responded_at' => now(),
    ]);

    return apiSuccess( "تم قبول الدعوة بنجاح.", $invitation->fresh());
}

public function decline(Request $request, ProjectInvitation $invitation)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة.");
    }

    if ($invitation->provider_profile_id != $profile->id) {
        return apiError("هذه الدعوة لا تخصك.");
    }

    if ($invitation->status != 'pending') {
        return apiError("تم الرد على هذه الدعوة مسبقاً.");
    }

    if ($invitation->expires_at && now()->gt($invitation->expires_at)) {
        return apiError("انتهت صلاحية هذه الدعوة.");
    }

    $invitation->update([
        'status' => 'declined',
        'responded_at' => now(),
    ]);

    return apiSuccess("تم رفض الدعوة.",$invitation->fresh() );
}

public function destroy(Request $request, ProjectInvitation $invitation)
{
    $this->authorize('update', $invitation->project);

    if ($invitation->status != 'pending') {
        return apiError("لا يمكن إلغاء دعوة تم الرد عليها.");
    }

    $invitation->delete();

    return apiSuccess("تم إلغاء الدعوة بنجاح.");
}






}