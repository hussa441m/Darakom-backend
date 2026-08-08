<?php

namespace App\Http\Controllers\Interaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    

//     public function complaints()
//     {

//         $complaints = Complaint::where(
//             'user_id',
//             request()->user()->id
//         )
//         ->with('project')
//         ->get();



//         return apiSuccess("شكاوي العميل",$complaints);
//     }



//      public function storeComplaint(Request $request)
//      {

//         $request->validate([

//             'text' => 'required|string',

//             'project_id' => 'required|exists:projects,id'

//         ]);



//         $project = Project::findOrFail(
//         $request->project_id
//         );

//         if ($project->client_id != request()->user()->id) {

//             return apiError("لا يمكنك إنشاء شكوى لهذا المشروع");

//         }



//         $complaint = Complaint::create([

//             'text' => $request->text,

//             'project_id' => $project->id,

//             'user_id' => request()->user()->id

//         ]);

//         return apiSuccess("تم إرسال الشكوى",$complaint);
//     }









// public function complaints(Request $request)
// {
//     $query = Complaint::with([
//         'project',
//         'user',
//         'againstUser',
//     ]);

//     if ($request->filled('type')) {
//         $query->where('type', $request->type);
//     }

//     $complaints = $query
//     ->where(function ($q) use ($request) {
//         $q->where('user_id', $request->user()->id)
//           ->orWhere('against_user_id', $request->user()->id);
//     })
//     ->latest()
//     ->get();

//     return apiSuccess("الشكاوى",$complaints);
// }

}
