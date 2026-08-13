<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of all project types with eager-loaded roles.
     */
    public function index()
    {
        try {
            $projectTypes = ProjectType::with('roles')->get();

            return apiSuccess('تم استرجاع جميع أنواع المشاريع بنجاح', $projectTypes);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع أنواع المشاريع: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display a specific project type with its linked roles.
     */
    public function show(ProjectType $projectType)
    {
        try {
            $projectType->load('roles');

            return apiSuccess('تم استرجاع بيانات نوع المشروع بنجاح', $projectType);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع بيانات نوع المشروع: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created project type with optional role associations.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:project_types,name',
                'roles' => 'nullable|array',
                'roles.*' => 'required|exists:roles,id',
            ]);



            $projectType = ProjectType::create([
                'name' => $validated['name'],
            ]);



            if (!empty($validated['roles'])) {
                $projectType->roles()->attach($validated['roles']);
            }



            $projectType->load('roles');

            return apiSuccess('تم إضافة نوع المشروع بنجاح', $projectType, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء إضافة نوع المشروع: ' . $e->getMessage(), null, 500);
        }
    }

   


    public function update(Request $request, ProjectType $projectType)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('project_types', 'name')->ignore($projectType->id),
                ],
                'roles' => 'nullable|array',
                'roles.*' => 'required|exists:roles,id',
            ]);




            $projectType->update([
                'name' => $validated['name'],
            ]);


            if (isset($validated['roles']) && !empty($validated['roles'])) {
                $projectType->roles()->sync($validated['roles']);
            } else {


            $projectType->roles()->detach();
            }


            $projectType->load('roles');

            return apiSuccess('تم تحديث نوع المشروع بنجاح', $projectType);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تحديث نوع المشروع: ' . $e->getMessage(), null, 500);
        }
    }



    public function destroy(ProjectType $projectType)
    {
        try {


        if ($projectType->projects()->exists()) {
                return apiError('لا يمكن حذف نوع المشروع لوجود مشاريع مرتبطة به', null, 400);
            }


            $projectType->roles()->detach();

            $projectType->delete();

            return apiSuccess('تم حذف نوع المشروع بنجاح');
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف نوع المشروع: ' . $e->getMessage(), null, 500);
        }
    }
}
