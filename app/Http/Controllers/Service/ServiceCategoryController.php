<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::all();

        return apiSuccess('تم استرجاع تصنيفات الخدمات بنجاح', $categories);
    }

    public function show($id)
    {
        $category = ServiceCategory::find($id);

        if (! $category) {
            return apiError('تصنيف الخدمة غير موجود', 404);
        }

        return apiSuccess('تم استرجاع تصنيف الخدمة بنجاح', $category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:service_categories,name',
            'display_name' => 'required',
        ]);

        $category = ServiceCategory::create($validated);

        return apiSuccess('تم إنشاء تصنيف الخدمة بنجاح', $category);
    }

    public function update(Request $request, $id)
    {
        $category = ServiceCategory::find($id);

        if (! $category) {
            return apiError('تصنيف الخدمة غير موجود', 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('service_categories', 'name')->ignore($category->id),
            ],
            'display_name' => 'required',
        ]);

        $category->update($validated);

        return apiSuccess('تم تعديل تصنيف الخدمة بنجاح', $category);
    }

    public function destroy($id)
    {
        $category = ServiceCategory::find($id);

        if (! $category) {
            return apiError('تصنيف الخدمة غير موجود', 404);
        }

        $category->delete();

        return apiSuccess('تم حذف تصنيف الخدمة بنجاح', null);
    }
}
