<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->get();
        return apiSuccess('All Projects', $projects);
    }


    function getCustomerProjects(Request $request)
    {
        $user = $request->user();
        $projects = Project::with(['documents' , 'projectType'])->where('customer_id', $user->id)
            ->latest()->get();
            $projects->each(function($project){
                $project->documents = $project->documents->map(function ($document) {
                    $document->path = asset("storage/$document->path");
                    return $document;
                });
            });
        return apiSuccess("مشاريعك", $projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->user()->type;
        $this->authorize('create', Project::class);
        
        $validated = $request->validate([
            'start_date' => 'required|date',
            'duration' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'location_details'  => 'required|string|max:255',
            'description' => 'required|string',
            'building_no' => 'required|string|max:15',
            'note' => 'nullable|string|max:1000',
            'project_type_id' => 'required|exists:project_types,id',
            'province_id' => 'required|exists:provinces,id',
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,png,jpeg,png,webp|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'required|max:255',
        ]);
        //يجب أن تتحدد أثناء تسجيل الدخول

        $validated['customer_id'] = $request->user()->id;
        $project = Project::create($validated);

        if ($request->has('documents')) {
            foreach ($request->documents as $document) {

                $docName = $document['file']->store('projects', 'public');

                $project->documents()->create([
                    'path' => $docName,
                    'description' => $document['description'],
                    'document_type_id' => $document['type'],
                ]);
            }
        }
        return apiSuccess("تم إضافة المشروع بنجاح", $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        $projects = $project->load('projectType', 'province', 'documents');

        $project->documents = $project->documents->map(function ($document) {
            $document->path = asset("storage/$document->path");
            return $document;
        });

        return apiSuccess("بيانات المشروع", $project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'start_date' => 'required|date',
            'duration' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'location_details'  => 'required|string|max:255',
            'description' => 'required|string',
            'building_no' => 'required|string|max:15',
            'note' => 'nullable|string|max:1000',
            'project_type_id' => 'required|exists:project_types,id',
            'province_id' => 'required|exists:provinces,id',
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'required|max:255',
        ]);
        $project->update($validated);
        /** معالجة تعديل الملفات */
        return apiSuccess("تم تعديل المشروع بنجاح", $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();
        return apiSuccess("تم حذف المشروع بنجاح");
    }

    function getSteps(Project $project)
    {
        $steps = $project->steps()->with('documents')->get()->map(function ($step) {
            $step->documents = $step->documents->map(function ($doc) {
                $doc->path = asset('storage/' . $doc->path);
                return $doc;
            });
            return $step;
        });
        return apiSuccess("خطوات المشروع", $steps);
    }
}
