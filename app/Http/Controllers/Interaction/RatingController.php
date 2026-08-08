<?php

namespace App\Http\Controllers\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Project $project, Request $request)
    {
        $this->authorize('update', $project);

        if (!$project->performed_by) {
            return apiError("لا يمكن تقييم مشروع لم يبدأ تنفيذه بعد.");
        }

        if ($project->execution_status !== 'finished') {
            return apiError("لا يمكن تقييم المشروع قبل اكتمال تنفيذه.");
        }

        $request->validate([
            'rate' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $providerUserId = $project->performer?->user_id;

        if (!$providerUserId) {
            return apiError("لم يتم العثور على مزود الخدمة.");
        }

        $exists = Rating::where([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'to_user_id' => $providerUserId,
        ])->exists();

        if ($exists) {
            return apiError("تم تقييم مزود الخدمة مسبقاً.");
        }

        $rating = Rating::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'to_user_id' => $providerUserId,
            'rate' => $request->rate,
            'comment' => $request->comment,
        ]);
        return apiSuccess("تم إضافة التقييم بنجاح.", $rating->load('project', 'user', 'toUser'));
    }
   
     public function update(Request $request, Rating $rating)
{
    if ($rating->user_id != $request->user()->id) {
        return apiError("غير مصرح لك بتعديل هذا التقييم.");
    }

    $request->validate([
        'rate' => 'required|integer|between:1,5',
        'comment' => 'nullable|string|max:1000',
    ]);

    $rating->update([
        'rate' => $request->rate,
        'comment' => $request->comment,
    ]);

    return apiSuccess(
        "تم تعديل التقييم بنجاح.",
        $rating->load('project', 'user', 'toUser')
    );
}

public function destroy(Request $request, Rating $rating)
{
    if ($rating->user_id != $request->user()->id) {
        return apiError("غير مصرح لك بحذف هذا التقييم.");
    }

    $rating->delete();

    return apiSuccess("تم حذف التقييم بنجاح.");
}

public function show(Rating $rating)
{
    return apiSuccess( "تفاصيل التقييم.",$rating->load([ 'project','user','toUser']));
}


public function myRatings(Request $request)
{
    $ratings = Rating::with([
        'project',
        'toUser'
    ])
    ->where('user_id', $request->user()->id)
    ->latest()
    ->get();
    return apiSuccess("تقييماتي.",$ratings);
}


public function providerRatings(Request $request)
{
    $ratings = Rating::with([
        'project',
        'user'
    ])
    ->where('to_user_id', $request->user()->id)
    ->latest()
    ->get();

    return apiSuccess("التقييمات التي حصلت عليها.",$ratings);
}

public function providerShow(Rating $rating, Request $request)
{
    if ($rating->to_user_id != $request->user()->id) {
        return apiError("غير مصرح لك بعرض هذا التقييم.");
    }

    return apiSuccess("تفاصيل التقييم.",$rating->load(['project','user']));
}


public function index()
{
    $ratings = Rating::with([
        'project',
        'user',
        'toUser'
    ])
    ->latest()
    ->get();

    return apiSuccess("جميع التقييمات.",$ratings);
}

public function adminShow(Rating $rating)
{
    return apiSuccess("تفاصيل التقييم.",
        $rating->load([
            'project',
            'user',
            'toUser'
        ])
    );
}

public function adminDestroy(Rating $rating)
{
    $rating->delete();

    return apiSuccess("تم حذف التقييم بنجاح.");
}









}