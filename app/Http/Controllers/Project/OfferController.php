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
    public function myOffers(Request $request)
    {
        $profile = $request->user()->profile;

       if (!$profile) {
          return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        $offers = Offer::with([ 'project.projectType','project.province','project.client',])
        ->where('offered_by', $profile->id)
        ->latest()
        ->get();

        return apiSuccess("عروضي",$offers);
    }

    public function store(Request $request, Project $project)
    {
         $profile = $request->user()->profile;

         if (!$profile) {
          return apiError("لا يوجد ملف شخصي لمزود الخدمة");
        }

        $validated = $request->validate([
          'cost' => 'required|numeric|min:0',
          'duration' => 'required|integer|min:1',
          'duration_unit' => 'required|in:hour,day',
          'provider_comment' => 'nullable|string',
          'details' => 'nullable|string',
        ]);

        $offer = Offer::create([
          'cost' => $validated['cost'],
          'duration' => $validated['duration'],
          'duration_unit' => $validated['duration_unit'],
          'provider_comment' => $validated['provider_comment'] ?? null,
          'details' => $validated['details'] ?? null,
          'project_id' => $project->id,
          'offered_by' => $profile->id,
          'status' => 'pending',
        ]);

        return apiSuccess("تم إرسال العرض بنجاح", $offer);
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
        'cost' => 'required|numeric|min:0',
        'duration' => 'required|integer|min:1',
        'duration_unit' => 'required|in:hour,day',
        'provider_comment' => 'nullable|string',
        'details' => 'nullable|string',
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
       'performed_by' => $offer->offered_by,
       'status' => 'active',
       'execution_status' => 'not_started'
       ]);


        $offer->provider->user
         ->notify(
          new AcceptProvider(
            $project->id,
            $offer->id
          )
       );


        return apiSuccess("تم قبول العرض" );
    }


   
    public function rejectOffer(Project $project, Offer $offer)
{
    $this->authorize('update', $project);


    if ($offer->project_id != $project->id) {
        return apiError("العرض لا يتبع لهذا المشروع");
    }

      if ($offer->status == 'accepted') {
        return apiError("لا يمكن رفض عرض تم قبوله");
    }


    $offer->update([
        'status' => 'rejected'
    ]);


    return apiSuccess( "تم رفض العرض");
}


}