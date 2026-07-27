<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Offer;
use App\Models\Rating;
use App\Models\Complaint;
use App\Notifications\AcceptProvider;
use App\Notifications\RejectOffer;
use Illuminate\Http\Request;


class ClientController extends Controller
{

    public function projects()
    {
        $projects = Project::where(
            'client_id',
            request()->user()->id
        )
        ->with([
            'offers',
            'performer.user',
            'province',
            'projectType'
        ])
        ->get();

        return apiSuccess("مشاريع العميل",$projects);
    }



    public function show(Project $project)
    {
        $this->authorize('update', $project);


        $project->load([
            'offers.provider.user',
            'performer.user',
            'documents',
            'reports'
        ]);


        return apiSuccess("تفاصيل المشروع",$project);
    }


    public function getOffers(Project $project)
    {
        $this->authorize('update', $project);


        $offers = $project->offers()
            ->with([
                'provider.user',
                'documents'
            ])
            ->get();


        return apiSuccess("عروض المشروع",$offers);
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


        // رفض باقي العروض
        $project->offers()
            ->where('id', '!=', $offer->id)
            ->update([
                'status' => 'rejected'
            ]);



        $project->update([
          'performed_by' => $offer->offered_by,
          'status' => 'active',
          'execution_status' => 'in_progress'
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



    public function rate(Project $project, Request $request)
   {
      $this->authorize('update', $project);


     if (!$project->performed_by) {

        return apiError(
            "لا يمكن تقييم مشروع لم يبدأ بعد"
        );
    }


    $request->validate([
        'rate' => 'required|integer|between:1,5',
        'comment' => 'nullable|string|max:1000'
    ]);


    $providerUserId = $project->performer?->user_id;


    if (!$providerUserId) {

        return apiError(
            "لم يتم العثور على مزود الخدمة"
        );
    }


    $exists = Rating::where([
        'project_id' => $project->id,
        'user_id' => request()->user()->id,
        'to_user_id' => $providerUserId
    ])->exists();


    if ($exists) {

        return apiError("تم تقييم هذا المزود مسبقاً");
    }



    Rating::create([

        'project_id' => $project->id,

        'user_id' => request()->user()->id,

        'to_user_id' => $providerUserId,

        'rate' => $request->rate,

        'comment' => $request->comment

    ]);


    return apiSuccess("تم إضافة التقييم بنجاح" );
   }




    public function complaints()
    {

        $complaints = Complaint::where(
            'user_id',
            request()->user()->id
        )
        ->with('project')
        ->get();



        return apiSuccess("شكاوي العميل",$complaints);
    }



     public function storeComplaint(Request $request)
     {

        $request->validate([

            'text' => 'required|string',

            'project_id' => 'required|exists:projects,id'

        ]);



        $project = Project::findOrFail(
        $request->project_id
        );

        if ($project->client_id != request()->user()->id) {

            return apiError("لا يمكنك إنشاء شكوى لهذا المشروع");

        }



        $complaint = Complaint::create([

            'text' => $request->text,

            'project_id' => $project->id,

            'user_id' => request()->user()->id

        ]);

        return apiSuccess("تم إرسال الشكوى",$complaint);
    }

}