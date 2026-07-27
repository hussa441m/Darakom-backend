<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::all();

        return apiSuccess('كافة أنواع المستندات ', $documentTypes);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:document_types',
        ]);
        $documentType = DocumentType::create($validated);
        return apiSuccess('تم إضافة نوع المستند بنجاح' , $documentType);
    }
        

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => "required|max:50|unique:document_types,name,documentType->id",
        ]);
        $documentType->update($validated);
        return apiSuccess('تم تعديل نوع المستند بنجاح' , $documentType);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documents()->count())
            return apiError('لا يمكن حذف نوع المستند لوجود مستندات مرتبطة بها', statusCode: 200);
        $documentType->delete();
        return apiSuccess('تم حذف نوع المستند بنجاح');
    }
}
