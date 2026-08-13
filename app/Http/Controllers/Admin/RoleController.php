<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles with eager-loaded project types.
     */
    public function index()
    {
        try {
            $roles = Role::with('projectTypes')->get();

            return apiSuccess('تم استرجاع جميع الأدوار بنجاح', $roles);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع الأدوار: ' . $e->getMessage(), null, 500);
        }
    }

    
    public function show(Role $role)
    {
        try {
            $role->load('projectTypes');

            return apiSuccess('تم استرجاع بيانات الدور بنجاح', $role);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع بيانات الدور: ' . $e->getMessage(), null, 500);
        }
    }

   


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:roles,name',
                'project_types' => 'nullable|array',
                'project_types.*' => 'required|exists:project_types,id',
            ]);



            $role = Role::create([
                'name' => $validated['name'],
            ]);



            if (!empty($validated['project_types'])) {
                $role->projectTypes()->attach($validated['project_types']);
            }



            $role->load('projectTypes');

            return apiSuccess('تم إضافة الدور بنجاح', $role, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء إضافة الدور: ' . $e->getMessage(), null, 500);
        }
    }

    


    public function update(Request $request, Role $role)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('roles', 'name')->ignore($role->id),
                ],
                'project_types' => 'nullable|array',
                'project_types.*' => 'required|exists:project_types,id',
            ]);



            $role->update([
                'name' => $validated['name'],
            ]);



            if (isset($validated['project_types']) && !empty($validated['project_types'])) {
                $role->projectTypes()->sync($validated['project_types']);
            } else {


            $role->projectTypes()->detach();
            }


            $role->load('projectTypes');

            return apiSuccess('تم تحديث الدور بنجاح', $role);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تحديث الدور: ' . $e->getMessage(), null, 500);
        }
    }

   


    public function destroy(Role $role)
    {
        try {

        if ($role->profiles()->exists()) {
                return apiError('لا يمكن حذف الدور لوجود ملفات شخصية مرتبطة به', null, 400);
            }

            $role->projectTypes()->detach();

            $role->delete();

            return apiSuccess('تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف الدور: ' . $e->getMessage(), null, 500);
        }
    }
}
