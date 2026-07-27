<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Project;
use App\Notifications\NewOffer;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    function getTotals(Request $request)
    {
        $profile = $request->user()->profile;


        $activeProject = Project::where('performed_by', $profile->id)->where('status', 'active')->count();
        $completedProject = Project::where('performed_by', $profile->id)->where('status', 'completed')->count();
        $rate = $profile->projects?->avg('rate');
        return apiSuccess("إحصائيات عن حسابك", compact('activeProject', 'completedProject', 'rate'));
    }
    function getNewProjects(Request $request)
    {
        $projectTypes = $request->user()->profile->projectTypes->modelKeys();
        $projects =  Project::with('projectType', 'province')->where('status', 'new')->whereIn('project_type_id', $projectTypes)->get();
        return apiSuccess("المشاريع الجديدة" . $request->user()->profile->id, $projects);
    }

    function getProjects(Request $request, $status)
    {
        $profile = $request->user()->profile;
        $projects =  Project::where('performed_by', $profile->id)->where('status', $status)->get();
        return apiSuccess("المشاريع التي حالتها $status", $projects);
    }
    function isActive(Request $request)
    {
        $user = $request->user();
        return apiSuccess("حالة المستخدم", ['is_active' => $user->status == 'active']);
    }

    function addOffer(Request $request, Project $project)
    {
        $user = $request->user();
        if ($user->status != 'active')
            return apiError("لا يمكنك إضافة عرض في الوقت الحالي، يجب أن يكون حسابك مفعلا");

        $validated =  $request->validate([
            'cost' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
            'details' => 'required',
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,png,jpeg,png,webp|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'required|max:255',
        ]);
        $validated['project_id'] = $project->id;
        $validated['offered_by'] = $user->profile->id;

        $offer = Offer::create($validated);
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {

                $docName = $document['file']->store('projects', 'public');

                $offer->documents()->create([
                    'path' => $docName,
                    'description' => $document['description'],
                    'document_type_id' => $document['type'],
                ]);
            }
        }
        $project->customer->notify(new NewOffer($user->name));

        return apiSuccess("تم إضافة عرضك", $offer);
    }

    function addStep(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100', 
            'description' => 'nullable|string|max:1000', 
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,png,jpeg,png,webp|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'required|max:255',
        ]);        
    $validated['project_id'] = $project->id;
        $step = $project->steps()->create($validated);
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {

                $docName = $document['file']->store('projects', 'public');

                $step->documents()->create([
                    'path' => $docName,
                    'description' => $document['description'],
                    'document_type_id' => $document['type'],
                ]);
            }
        }
        return apiSuccess("تم إضافة الخطوة بنجاح", $step);
    }

    function endProject(Project $project){
        $project->status = 'completed';
        $project->save();
        return apiSuccess("تم إنهاء المشروع بنجاح");;   
    }
    
}
