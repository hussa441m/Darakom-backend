<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ArtisanServiceController extends Controller
{
    public function getProvidersByCategory($categoryId)
    {
        $category = ServiceCategory::with(['profiles.user'])->find($categoryId);

        if (! $category) {
            return apiError('تصنيف الخدمة غير موجود', 404);
        }

        return apiSuccess('تم تحميل قائمة الحرفيين بنجاح', $category);
    }

    public function toggleProviderService(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
        ]);

        $profile = $request->user()->profile;

        if (! $profile) {
            return apiError('الملف الشخصي للحرفي غير موجود', 404);
        }

        $result = $profile->serviceCategories()->toggle($validated['service_category_id']);
        
        // نحافظ على الكلمات المفتاحية بالإنكليزية من أجل مبرمجي الواجهات (Front-End)
        $action = count($result['attached']) ? 'attached' : (count($result['detached']) ? 'detached' : 'untouched');

        // نخصص الرسالة العربية بناءً على الحدث
        $message = $action === 'attached' ? 'تمت إضافة الخدمة لبروفايلك بنجاح' : ($action === 'detached' ? 'تمت إزالة الخدمة من بروفايلك بنجاح' : 'لم يتم إجراء أي تغيير');

        return apiSuccess($message, [
            'profile_id' => $profile->id,
            'service_category_id' => $validated['service_category_id'],
            'action' => $action,
        ]);
    }
}