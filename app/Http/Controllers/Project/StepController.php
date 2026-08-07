<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $steps = $project->steps()
            ->with(['reports', 'documents'])
            ->latest()
            ->get();

        return apiSuccess('خطوات المشروع', $steps);
    }

    public function show(Request $request, Project $project, Step $step)
    {
        $this->authorize('update', $project);

        if ($step->project_id != $project->id) {
            return apiError('هذه الخطوة لا تتبع لهذا المشروع.');
        }

        $step->load(['reports', 'documents']);

        return apiSuccess('تفاصيل الخطوة', $step);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'date' => 'nullable|date',
        ]);

        $validated['project_id'] = $project->id;
        $validated['progress_percent'] = 0;
        $validated['status'] = 'not_started';

        $step = Step::create($validated);

        return apiSuccess('تم إنشاء الخطوة بنجاح.', $step);
    }

    public function update(Request $request, Step $step)
    {
        $this->authorize('update', $step->project);

       $validated = $request->validate([
    'title' => 'sometimes|required|string|max:100',
    'description' => 'nullable|string|max:1000',
    'date' => 'nullable|date',
    'progress_percent' => 'sometimes|integer|min:0|max:100',
    'status' => 'sometimes|in:not_started,in_progress,completed',
]);
        $step->update($validated);

        return apiSuccess('تم تعديل الخطوة بنجاح.', $step->fresh()->load(['reports', 'documents']));
    }

    public function destroy(Request $request, Step $step)
    {
        $this->authorize('update', $step->project);

        $step->delete();

        return apiSuccess('تم حذف الخطوة بنجاح.');
    }

    public function clientIndex(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $steps = $project->steps()
            ->with(['reports', 'documents'])
            ->latest()
            ->get();

        return apiSuccess('خطوات المشروع', $steps);
    }

    public function clientShow(Request $request, Project $project, Step $step)
    {
        $this->authorize('view', $project);

        if ($step->project_id != $project->id) {
            return apiError('هذه الخطوة لا تتبع لهذا المشروع.');
        }

        $step->load(['reports', 'documents']);

        return apiSuccess('تفاصيل الخطوة', $step);
    }

    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Step::class);

        $steps = Step::with(['project', 'reports', 'documents'])
            ->latest()
            ->paginate(15);

        return apiSuccess('جميع الخطوات', $steps);
    }

    public function adminShow(Request $request, Step $step)
    {
        $this->authorize('view', $step);

        $step->load(['project', 'reports', 'documents']);

        return apiSuccess('تفاصيل الخطوة', $step);
    }

    public function adminDestroy(Request $request, Step $step)
    {
        $this->authorize('delete', $step);

        $step->delete();

        return apiSuccess('تم حذف الخطوة بنجاح.');
    }
}
