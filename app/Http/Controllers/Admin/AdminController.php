<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Offer;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;

class AdminController extends Controller
{
    public function getComplaints()
    {
        $complaints = Complaint::with('user:id,name', 'project:id,description,performed_by', 'project.client:id,user_id', 'project.client.user:id,name')->get();

        return apiSuccess('الشكاوى', $complaints);
    }

    public function totals()
    {
        $users = [
            'total' => User::where('type', '!=', 'admin')->count(),
            'clients' => User::where('type', 'client')->count(),
            'engineers' => Profile::whereIn('role_id', [2, 3, 4])->count(),
            'offices' => Profile::where('role_id', 5)->count(),
            'contractors' => Profile::where('role_id', 1)->count(),
            'craftsmen' => Profile::where('role_id', 6)->count(),
        ];

        $projects = [
            'total' => Project::count(),
            'open' => Project::whereIn('status', ['new', 'pending'])->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'paused' => Project::where('execution_status', 'paused')->count(),
        ];

        $offers = [
            'total' => Offer::count(),
            'accepted' => Offer::where('status', 'accepted')->count(),
        ];

        $badges = [
            'pending_providers_count' => User::where('type', 'provider')->where('status', 'pending')->count(),
            'active_complaints_count' => Complaint::whereIn('status', ['pending', 'under_review'])->count(),
        ];

        return apiSuccess('Dashboard Statistics', [
            'users' => $users,
            'projects' => $projects,
            'offers' => $offers,
            'badges' => $badges,
        ]);
    }
}
