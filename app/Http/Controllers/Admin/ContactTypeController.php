<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactTypeController extends Controller
{
    /**
     * Display a listing of all contact types.
     */
    public function index()
    {
        try {
            $contactTypes = ContactType::all();

            return apiSuccess('تم استرجاع جميع أنواع جهات الاتصال بنجاح', $contactTypes);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع أنواع جهات الاتصال: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display a specific contact type.
     */
    public function show(ContactType $contactType)
    {
        try {
            return apiSuccess('تم استرجاع بيانات نوع جهة الاتصال بنجاح', $contactType);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع بيانات نوع جهة الاتصال: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created contact type.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:contact_types,name',
            ]);

            $contactType = ContactType::create($validated);

            return apiSuccess('تم إضافة نوع جهة الاتصال بنجاح', $contactType, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء إضافة نوع جهة الاتصال: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update an existing contact type.
     */
    public function update(Request $request, ContactType $contactType)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('contact_types', 'name')->ignore($contactType->id),
                ],
            ]);

            $contactType->update($validated);

            return apiSuccess('تم تحديث نوع جهة الاتصال بنجاح', $contactType);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تحديث نوع جهة الاتصال: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete a contact type.
     * Check for related contacts first.
     */
    public function destroy(ContactType $contactType)
    {
        try {
            // Check if this contact type has associated contacts
            if ($contactType->contacts()->exists()) {
                return apiError('لا يمكن حذف نوع جهة الاتصال لوجود جهات اتصال مرتبطة به', null, 400);
            }

            // Delete the contact type
            $contactType->delete();

            return apiSuccess('تم حذف نوع جهة الاتصال بنجاح');
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف نوع جهة الاتصال: ' . $e->getMessage(), null, 500);
        }
    }
}
