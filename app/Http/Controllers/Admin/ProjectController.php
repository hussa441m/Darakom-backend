<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  
    public function index(Request $request)
    {
        $query = Project::with([
            'projectType',
            'province',
            'client',
            'providerProfile.user'
        ]);

        // الفلترة حسب حالة التقييم/المراجعة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // الفلترة حسب نوع المناقصة (عامة / خاصة / مستعجلة)
        if ($request->filled('invitation_type')) {
            $query->where('invitation_type', $request->invitation_type);
        }

        // البحث باسم المشروع أو بيانات العميل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($c) use ($search) {
                      // تم التعديل ليطابق حقل name في جدول Users
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->latest()->paginate($request->get('per_page', 15));

        return apiSuccess("قائمة المشاريع", $projects);
    }

   
    public function show(Project $project)
    {
        $project->load([
            'projectType',
            'province',
            'client',
            'providerProfile.user',
            'offers.provider.user',
            'documents',
            'reports',
            'invitations.provider.user'
        ]);

        return apiSuccess("تفاصيل المشروع", $project);
    }

   
    public function approve(Project $project)
    {
        if ($project->status === 'approved' || $project->status === 'open') {
            return apiError('المشروع معتمد ومطروح مسبقاً.');
        }

        $project->update([
            'status' => 'open', 
        ]);

        return apiSuccess('تمت الموافقة على المشروع وطرحه بنجاح.', $project);
    }

    public function reject(Request $request, Project $project)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $project->update([
            'status' => 'rejected',
            'comment' => $validated['rejection_reason'], 
        ]);

        return apiSuccess('تم رفض المشروع وتسجيل السبب.', $project);
    }
}