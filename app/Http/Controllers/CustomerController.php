<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Offer;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Role;
use App\Notifications\AcceptOffer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{

    function getClients(Role $role)
    {
        $profiles = $role->profiles()->with('user')->get();
        return apiSuccess("العملاء - ($role->name)",  ProfileResource::collection($profiles));
    }


    function getOffers(Project $project)
    {
        $offers = $project->offers()->with('documents')->get()->map(function ($offer) {
            $offer->documents = $offer->documents->map(function ($doc) {
                $doc->path = asset('storage/' . $doc->path);
                return $doc;
            });
            return $offer;
        });

        return apiSuccess("عروض المشروع", $offers);
    }

    function acceptOffer(Project $project, Offer $offer)
    {
        // return $offer;
        $offer->update(['isSelected' => true]);
        $project->update(['performed_by' => $offer->offered_by, 'status' => 'active']);
        $clientuser = Profile::find($offer->offered_by)->user;

        $clientuser->notify(new AcceptOffer());
        return apiSuccess("تم قبول العرض");
    }

    

    function rate(Project $project, Request $request)
    {
        $this->authorize('update', $project);

        $request->validate([
            'rate' => 'required|in:1,2,3,4,5',
            'comment' => 'nullable|string',
        ]);

        $project->update(['rate' => $request->rate  , 'comment' => $request->comment]);

        return apiSuccess("تم إضافة التقييم بنجاح");
    }
}
