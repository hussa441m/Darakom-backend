<?php

namespace App\Http\Controllers\Interaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Return complaints submitted against the authenticated user
     */
    public function complaintsAgainstMe(Request $request)
    {
        $user = $request->user();

        $complaints = Complaint::with(['project', 'user'])
            ->where('against_user_id', $user->id)
            ->latest()
            ->get();

        return apiSuccess('الشكاوى المقدمة ضدك.', $complaints);
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
        // تم إضافة againstUser لجلب بيانات المشتكى عليه
        $complaints = Complaint::with(['user', 'project', 'againstUser'])
            ->latest()
            ->get();

        // إعادة تشكيل البيانات لتتوافق مع ما يتوقعه الفرونت إند تماماً
        $formattedComplaints = $complaints->map(function ($complaint) {
            return [
                'id' => $complaint->id,
                'fromName' => $complaint->user->name ?? 'غير معروف',
                'againstName' => $complaint->againstUser->name ?? 'غير معروف',
                'projectTitle' => $complaint->project->title ?? 'غير محدد',
                'date' => $complaint->created_at->format('Y/m/d'),
                'status' => $complaint->status ?? 'pending',
                'description' => $complaint->text, // في الداتابيز اسمها text وفي الفرونت description
                'adminReply' => $complaint->admin_response, // في الداتابيز admin_response
            ];
        });

        return apiSuccess('جميع الشكاوى', $formattedComplaints);
    }

    /**
     * Admin: take action on a complaint (update status, admin notes,
     * optionally change reported user's status)
     */
    public function takeAction(Request $request, Complaint $complaint)
    {
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

    // ==========================================
    // الدوال الجديدة المضافة خصيصاً للفرونت إند (React)
    // ==========================================

    /**
     * Admin: Reply to a complaint and resolve it
     */
    public function replyToComplaint(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string'
        ]);

        $complaint = Complaint::findOrFail($id);
        
        $complaint->admin_response = $request->reply;
        $complaint->status = 'resolved';
        $complaint->save();

        return apiSuccess('تم إرسال الرد وحل الشكوى بنجاح', $complaint);
    }

    /**
     * Admin: Close a complaint without a specific reply
     */
    public function closeComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        
        $complaint->status = 'closed';
        $complaint->save();

        return apiSuccess('تم إغلاق الشكوى بنجاح', $complaint);
    }
}