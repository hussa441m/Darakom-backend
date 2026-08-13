<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Project;
use App\Notifications\AcceptProvider;
use App\Notifications\RejectOffer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
   
    public function publicOffers(Request $request)
    {
        $offers = Offer::whereHas('project', function ($q) use ($request) {
            $q->where('client_id', $request->user()->id)
              ->where('invitation_type', 'public');
        })
        ->with([
            'project:id,title,invitation_type',
            'provider.user:id,first_name,last_name,avatar',
            'provider.role:id,name',
        ])
        ->latest()
        ->get();

        return apiSuccess("العروض العامة الموجهة لك", $offers);
    }
 
    public function privateOffers(Request $request)
    {
      $offers = Offer::whereHas('project', function ($q) use ($request) {
        $q->where('client_id', $request->user()->id)
          ->where('invitation_type', 'private'); // تصفية العروض الخاصة فقط
    })
    ->with([
        'project:id,title,invitation_type',
        'provider.user:id,first_name,last_name,avatar',
        'provider.role:id,name',
    ])
    ->latest()
    ->get();
   
    $formattedOffers = $offers->map(function ($offer) {
        return [
            'id'               => $offer->id,
            'status'           => $offer->status, // pending, accepted, rejected
            'cost'             => $offer->cost,
            'duration'         => $offer->duration,
            'duration_unit'    => $offer->duration_unit,
            'provider_comment' => $offer->provider_comment ?? $offer->details,
            'created_at'       => $offer->created_at->format('Y/m/d'),
            'provider'         => [
                'name'           => $offer->provider->user->full_name ?? '',
                'avatar'         => $offer->provider->user->avatar ?? null,
                'role_name'      => $offer->provider->role->name ?? 'مزود خدمة',
                'average_rating' => $offer->provider->average_rating ?? 0,
            ],
            'project'          => [
                'id'    => $offer->project->id,
                'title' => $offer->project->title,
            ],
        ];
    });

    return apiSuccess("العروض الخاصة الموجهة لك", $formattedOffers);
}
    public function myOffers(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        $offers = Offer::with(['project.projectType', 'project.province', 'project.client'])
            ->where('offered_by', $profile->id)
            ->latest()
            ->get();

        return apiSuccess("عروضي", $offers);
    }

    public function store(Request $request, Project $project)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        $validated = $request->validate([
            'cost'             => 'required|numeric|min:0',
            'duration'         => 'required|integer|min:1',
            'duration_unit'    => 'required|in:day,month,year', // تم تعديلها لتدعم الأيام والشهور والسنوات
            'provider_comment' => 'nullable|string',
            'details'          => 'nullable|string',
        ]);

        $offer = Offer::create([
            'cost'             => $validated['cost'],
            'duration'         => $validated['duration'],
            'duration_unit'    => $validated['duration_unit'],
            'provider_comment' => $validated['provider_comment'] ?? null,
            'details'          => $validated['details'] ?? null,
            'project_id'       => $project->id,
            'offered_by'       => $profile->id,
            'status'           => 'pending',
        ]);

      
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('offer_documents', 'public');
                $offer->documents()->create([
                    'path'        => $path,
                    'description' => 'مرفق عرض سعر',
                ]);
            }
        }

        return apiSuccess("تم إرسال العرض بنجاح", $offer->load('documents'));
    }

 
    public function update(Request $request, Offer $offer)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        if ($offer->offered_by != $profile->id) {
            return apiError("لا يمكنك تعديل هذا العرض");
        }

        $validated = $request->validate([
            'cost'             => 'required|numeric|min:0',
            'duration'         => 'required|integer|min:1',
            'duration_unit'    => 'required|in:day,month,year',
            'provider_comment' => 'nullable|string',
            'details'          => 'nullable|string',
        ]);

        $offer->update($validated);

        return apiSuccess("تم تعديل العرض بنجاح", $offer);
    }

    public function destroy(Request $request, Offer $offer)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        if ($offer->offered_by != $profile->id) {
            return apiError("لا يمكنك حذف هذا العرض");
        }

        $offer->delete();

        return apiSuccess("تم حذف العرض بنجاح");
    }

    public function acceptOffer(Project $project, Offer $offer)
    {
        $this->authorize('update', $project);

        if ($offer->project_id != $project->id) {
            return apiError("العرض لا يتبع لهذا المشروع");
        }

        $offer->update([
            'status' => 'accepted'
        ]);

        $project->offers()
            ->where('id', '!=', $offer->id)
            ->update([
                'status' => 'rejected'
            ]);

        $project->update([
            'performed_by'        => $offer->offered_by,
            'provider_profile_id' => $offer->offered_by,
            'status'              => 'active',
            'execution_status'    => 'in_progress'
        ]);

        if ($offer->provider && $offer->provider->user) {
            $offer->provider->user->notify(
                new AcceptProvider(
                    $project->id,
                    $offer->id
                )
            );
        }

        return apiSuccess("تم قبول العرض وبدء المشروع بنجاح");
    }
   
public function show(Project $project, Offer $offer)
{
     $this->authorize('update', $project);

    if ($offer->project_id !== $project->id) {
        return apiError("العرض لا يتبع لهذا المشروع", 404);
    }

    $offer->load([
        'project:id,title,client_id,work_type,area',
        'provider.user:id,first_name,last_name,avatar',
        'provider.role:id,name',
        'documents',
    ]);

    $responseData = [
        'id'                  => $offer->id,
        'status'              => $offer->status,
        'status_label'        => match($offer->status) {
            'pending'  => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            default    => 'قيد الدراسة',
        },
        'cost'                => $offer->cost,
        'duration'            => $offer->duration,
        'duration_unit'       => $offer->duration_unit,
        'provider_comment'    => $offer->provider_comment,
        'details'             => $offer->details,
        'created_at'          => $offer->created_at->format('Y/m/d'),
        'provider'            => [
            'id'             => $offer->provider->id,
            'name'           => $offer->provider->user->full_name,
            'avatar'         => $offer->provider->user->avatar,
            'role_name'      => $offer->provider->role->name ?? 'مزود خدمة',
            'average_rating' => $offer->provider->average_rating,
        ],
        'project'             => [
            'id'    => $offer->project->id,
            'title' => $offer->project->title,
        ],
        'documents'           => $offer->documents->map(function ($doc) {
            return [
                'id'          => $doc->id,
                'path'        => asset('storage/' . $doc->path),
                'description' => $doc->description,
                'file_type'   => pathinfo($doc->path, PATHINFO_EXTENSION) === 'pdf' ? 'pdf' : 'image',
            ];
        }),
    ];

    return apiSuccess("تفاصيل العرض", $responseData);
}

    public function rejectOffer(Request $request, Project $project, Offer $offer)
    {
        $this->authorize('update', $project);

        if ($offer->project_id != $project->id) {
            return apiError("العرض لا يتبع لهذا المشروع");
        }

        if ($offer->status == 'accepted') {
            return apiError("لا يمكن رفض عرض تم قبوله");
        }

        $validated = $request->validate([
            'reject_reason' => 'nullable|string|max:500'
        ]);

        $offer->update([
            'status'        => 'rejected',
            'reject_reason' => $validated['reject_reason'] ?? null
        ]);

      
        if ($offer->provider && $offer->provider->user) {
            $offer->provider->user->notify(
                new RejectOffer(
                    $offer->id,
                    $project->id
                )
            );
        }

        return apiSuccess("تم رفض العرض وإرسال الإشعار بنجاح");
    }
}