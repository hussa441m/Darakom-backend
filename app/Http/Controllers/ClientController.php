<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Offer;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_projects'    => $user->projects()->count(),
            'in_progress_count' => $user->projects()->where('execution_status', 'in_progress')->count(),
            'completed_count'   => $user->projects()->where('execution_status', 'finished')->count(),
            'pending_offers'    => Offer::whereHas('project', function ($query) use ($user) {
                                        $query->where('client_id', $user->id);
                                    })->where('status', 'pending')->count(),
        ];

        $projects = [
            'in_progress' => $user->projects()
                ->where('execution_status', 'in_progress')
                ->with(['province', 'performer.user', 'performer.role'])
                ->latest()
                ->take(2)
                ->get(),

            'pending' => $user->projects()
                ->where('execution_status', 'not_started')
                ->with(['province', 'providerProfile.user'])
                ->withCount('offers')
                ->latest()
                ->take(2)
                ->get(),

            'completed' => $user->projects()
                ->where('execution_status', 'finished')
                ->with(['province', 'performer.user', 'ratings'])
                ->latest()
                ->take(2)
                ->get(),
        ];

        $recentOffers = Offer::whereHas('project', function ($query) use ($user) {
            $query->where('client_id', $user->id);
        })
        ->with([
            'provider.user',
            'provider.role',
            'project:id,title'
        ])
        ->latest()
        ->take(4)
        ->get();

        return apiSuccess("بيانات لوحة التحكم", [
            'user_info'     => [
                'id'        => $user->id,
                'full_name' => $user->full_name,
            ],
            'stats'         => $stats,
            'projects'      => $projects,
            'recent_offers' => $recentOffers,
        ]);
    }

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

        return apiSuccess("مشاريع العميل", $projects);
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

        return apiSuccess("تفاصيل المشروع", $project);
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

        return apiSuccess("عروض المشروع", $offers);
    }
}