<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return apiSuccess('تم جلب إعدادات الموقع بنجاح', $settings);
    }

    public function update(Request $request)
    {
        // 1. التحقق من صحة البيانات القادمة من الواجهة
        $validator = Validator::make($request->all(), [
            'site_name' => 'nullable|string|max:255',
            'site_slogan' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:255',
            'guide_intro' => 'nullable|string',
            'guide_financial_advice' => 'nullable|string',
            'guide_general_instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // 2. تحديث أو إنشاء الإعدادات
        $data = $request->except('_token'); // استبعاد أي توكن إن وجد

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $updatedSettings = Setting::pluck('value', 'key')->toArray();

        return apiSuccess('تم حفظ جميع التغييرات بنجاح', $updatedSettings);
    }
}