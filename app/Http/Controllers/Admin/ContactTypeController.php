<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContactType;

class ContactTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactTypes = ContactType::all();

        return apiSuccess('كافة أنواع جهات الاتصال ', $contactTypes);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50',
        ]);
        $contactType = ContactType::create($validated);
        return apiSuccess('تم إضافة نوع جهة الاتصال بنجاح' , $contactType);
    }
        

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactType $contactType)
    {
        $validated = $request->validate([
            'name' => 'required|max:50',
        ]);
        $contactType->update($validated);
        return apiSuccess('تم تعديل نوع جهة الاتصال بنجاح' , $contactType);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactType $contactType)
    {
        if ($contactType->contacts()->count())
            return apiError('لا يمكن حذف نوع جهة الاتصال لوجود جهات اتصال مرتبطة بها', statusCode: 200);
        $contactType->delete();
        return apiSuccess('تم حذف نوع جهة الاتصال بنجاح');
    }
}
