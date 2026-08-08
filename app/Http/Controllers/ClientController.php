<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Offer;




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




 }