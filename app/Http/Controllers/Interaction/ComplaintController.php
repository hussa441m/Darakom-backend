<?php

namespace App\Http\Controllers\Interaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
<<<<<<< HEAD

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
=======
use App\Models\Complaint;
use App\Models\User;

class ComplaintController extends Controller
{
	/**
	 * Store a new complaint (client or provider)
	 */
	public function store(Request $request)
	{
		$user = $request->user();

		$validated = $request->validate([
			'text' => 'required|string',
			'type' => 'nullable|string',
			'project_id' => 'nullable|exists:projects,id',
			'against_user_id' => 'nullable|exists:users,id',
		]);

		$validated['user_id'] = $user->id;
		$validated['status'] = 'pending';

		$complaint = Complaint::create($validated);

		return apiSuccess('تم إرسال الشكوى بنجاح.', $complaint);
	}

	/**
	 * Return authenticated user's complaints with project details
	 */
	public function myComplaints(Request $request)
	{
		$user = $request->user();

		$complaints = Complaint::with(['project'])
			->where('user_id', $user->id)
			->latest()
			->get();

		return apiSuccess('شكاويك', $complaints);
	}

	/**
	 * Show a single complaint (only owner or target can view)
	 */
	public function show(Request $request, Complaint $complaint)
	{
		$user = $request->user();

		if ($complaint->user_id !== $user->id && $complaint->against_user_id !== $user->id) {
			return apiError('لا يمكنك عرض هذه الشكوى.');
		}

		$complaint->load(['project', 'user', 'againstUser']);

		return apiSuccess('تفاصيل الشكوى', $complaint);
	}

	/**
	 * Admin: list all complaints with related user and project
	 */
	public function index(Request $request)
	{
		$this->authorize('viewAny', Complaint::class);

		$complaints = Complaint::with(['user', 'project'])
			->latest()
			->get();

		return apiSuccess('جميع الشكاوى', $complaints);
	}

	/**
	 * Admin: take action on a complaint (update status, admin notes,
	 * optionally change reported user's status)
	 */
	public function takeAction(Request $request, Complaint $complaint)
	{
		$this->authorize('update', $complaint);

		$validated = $request->validate([
			'status' => 'required|string',
			'admin_response' => 'nullable|string',
			'against_user_status' => 'nullable|string',
		]);

		$complaint->status = $validated['status'];

		if (array_key_exists('admin_response', $validated)) {
			$complaint->admin_response = $validated['admin_response'];
		}

		$complaint->save();

		if (!empty($validated['against_user_status']) && $complaint->against_user_id) {
			$reported = User::find($complaint->against_user_id);
			if ($reported) {
				$reported->status = $validated['against_user_status'];
				$reported->save();
			}
		}

		return apiSuccess('تم تنفيذ الإجراء على الشكوى.', $complaint->fresh()->load(['user', 'project', 'againstUser']));
	}
>>>>>>> 643e4daddb03e42b02823267524128591be6c47e

}
