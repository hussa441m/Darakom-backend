<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getComplaints()
    {
        $complaints = Complaint::with( 'user:id,name','project:id,description,performed_by', 'project.client:id,user_id' , 'project.client.user:id,name' )->get();
        return apiSuccess('الشكاوى ',  $complaints);
    }

        

    public function totals()
    {
        $profileCount = Profile::count();
        return apiSuccess("إجماليات", [
            'projects' => \App\Models\Project::count(),
            'clients' => $profileCount,
            'customer' => User::count() - $profileCount -1,
        ]);
    }


    
}
