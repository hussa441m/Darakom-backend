<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class ٌRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('projectTypes')->get();

        return apiSuccess('كافة الأدوار ', $roles);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:roles',
            'projectTypes' => 'nullable|array',
            'projectTypes.*' => 'required|exists:project_types,id',
        ]);
        $roles = Role::create($validated);
        $roles->projectTypes()->attach($request->projectTypes);
        return apiSuccess('تم إضافة الدور بنجاح', $roles);
    }


    /**
     * Update the specified resource in storage.
     */
    public function show(Role $role)
    {
        $role = Role::with('projectTypes')->get();
        return apiSuccess('معلومات الدور مع أنواعه', $role);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => "required|max:50|unique:roles,name,$role->id",
            'projectTypes' => 'nullable|array',
            'projectTypes.*' => 'required|exists:project_types,id',
        ]);
        $role->update($validated);
        $role->projectTypes()->sync($request->projectTypes);
        return apiSuccess('تم تعديل الدور بنجاح', $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return apiSuccess('تم حذف الدور بنجاح');
    }
}
