<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProjectType;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectTypes = ProjectType::with('roles')->get();

        return apiSuccess('كافة أنواع المشاريع ', $projectTypes);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:project_types',
            'roles' => 'nullable|array',
            'roles.*' => 'required|exists:project_types,id',
        ]);
        $projectType = ProjectType::create($validated);
        $projectType->roles()->attach($request->roles);
        return apiSuccess('تم إضافة نوع المشروع بنجاح' , $projectType);
    }
        

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectType $projectType)
    {
        $validated = $request->validate([
            'name' => "required|max:50|unique:project_types,name,$projectType->id",
            'roles' => 'nullable|array',
            'roles.*' => 'required|exists:project_types,id',
        ]);
        $projectType->update($validated);
        $projectType->roles()->sync($request->roles);

        return apiSuccess('تم تعديل نوع المشروع بنجاح' , $projectType);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectType $projectType)
    {
        if ($projectType->projects()->count())
            return apiError('لا يمكن حذف نوع المشروع لوجود مشاريع مرتبطة بها', statusCode: 200);
        $projectType->delete();
        return apiSuccess('تم حذف نوع المشروع بنجاح');
    }
}
