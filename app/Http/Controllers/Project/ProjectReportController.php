<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectReport;
use Illuminate\Http\Request;

class ProjectReportController extends Controller
{
public function store(Request $request, Project $project)
{
    $user = $request->user();

    if (!$user->profile) {
        return apiError("لا يوجد ملف شخصي لمزود الخدمة.");
    }

    if ($project->performed_by != $user->profile->id) {
        return apiError("أنت لست مزود الخدمة المنفذ لهذا المشروع.");
    }

    if ($project->status != 'active') {
        return apiError("لا يمكن إضافة تقرير قبل بدء تنفيذ المشروع.");
    }

    $validated = $request->validate([
        'description' => 'required|string',
        'reported_progress' => 'nullable|integer|min:0|max:100',
        'step_id' => 'nullable|exists:steps,id',
    ]);

    $validated['project_id'] = $project->id;
    $validated['user_id'] = $user->id;
    $validated['status'] = 'pending';

    $report = ProjectReport::create($validated);

    return apiSuccess( "تم إرسال التقرير بنجاح.", $report);
}


public function index(Request $request, Project $project)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError('لا يوجد ملف شخصي لمزود الخدمة.');
    }

    if ($project->performed_by != $profile->id) {
        return apiError('لا يمكنك عرض تقارير هذا المشروع.');
    }

    $reports = ProjectReport::with([
        'user',
        'step',
        'documents',
    ])
    ->where('project_id', $project->id)
    ->latest()
    ->get();

    return apiSuccess('تقارير المشروع', $reports );
}

public function show(Request $request, Project $project, ProjectReport $report)
{
    $profile = $request->user()->profile;

    if (!$profile) {
        return apiError('لا يوجد ملف شخصي لمزود الخدمة.');
    }

    if ($project->performed_by != $profile->id) {
        return apiError('لا يمكنك عرض هذا التقرير.');
    }

    if ($report->project_id != $project->id) {
        return apiError('هذا التقرير لا يتبع لهذا المشروع.');
    }

    $report->load([
        'user',
        'step',
        'documents',
    ]);

    return apiSuccess( 'تفاصيل التقرير', $report);
}

public function update(Request $request, ProjectReport $report)
{
    if ($report->user_id != $request->user()->id) {
        return apiError("لا يمكنك تعديل هذا التقرير.");
    }

    if ($report->project->status == 'completed') {
        return apiError("لا يمكن تعديل تقرير بعد انتهاء المشروع.");
    }

    $validated = $request->validate([
        'description' => 'required|string',
        'reported_progress' => 'nullable|integer|min:0|max:100',
        'step_id' => 'nullable|exists:steps,id',
    ]);

    $report->update($validated);

    return apiSuccess(
        "تم تعديل التقرير بنجاح.",
        $report->load('step', 'documents')
    );
}
public function destroy(Request $request, ProjectReport $report)
{
    if ($report->user_id != $request->user()->id) {
        return apiError("لا يمكنك حذف هذا التقرير.");
    }

    if ($report->project->status == 'completed') {
        return apiError("لا يمكن حذف تقرير بعد انتهاء المشروع.");
    }

    $report->delete();

    return apiSuccess("تم حذف التقرير بنجاح.");
}



public function clientIndex(Request $request, Project $project)
{
    $this->authorize('view', $project);

    $reports = $project->reports()
        ->with([
            'user',
            'step',
            'documents',
        ])
        ->latest()
        ->get();

    return apiSuccess(
        'تقارير المشروع',
        $reports
    );
}

public function clientShow(Request $request, Project $project, ProjectReport $report)
{
    $this->authorize('view', $project);

    if ($report->project_id != $project->id) {
        return apiError('هذا التقرير لا يتبع لهذا المشروع.');
    }

    $report->load([
        'user',
        'step',
        'documents',
    ]);

    return apiSuccess(
        'تفاصيل التقرير',
        $report
    );
}






}