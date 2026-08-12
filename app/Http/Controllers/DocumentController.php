<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function getTypes()
    {
        return apiSuccess('تم استرجاع أنواع المستندات بنجاح', DocumentType::all());
    }

    public function index(Request $request)
    {
        $profile = $request->user()->profile;
        if (! $profile) {
            return apiError('الملف الشخصي لمزود الخدمة غير موجود', null, 404);
        }

        $documents = $profile->documents()
            ->with('documentType')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Document $document) {
                return [
                    'id' => $document->id,
                    'document_type_id' => $document->document_type_id,
                    'document_type_name' => $document->documentType?->name,
                    'description' => $document->description,
                    'path' => $document->path,
                    'url' => asset('storage/' . $document->path),
                    'created_at' => $document->created_at,
                ];
            });

        return apiSuccess('تم استرجاع مستندات المزود بنجاح', $documents);
    }

    public function store(Request $request)
    {
        $profile = $request->user()->profile;
        if (! $profile) {
            return apiError('الملف الشخصي لمزود الخدمة غير موجود', null, 404);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10000',
            'document_type_id' => 'required|exists:document_types,id',
            'description' => 'nullable|string|max:255',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = $profile->documents()->create([
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'document_type_id' => $validated['document_type_id'],
        ]);

        return apiSuccess('تم رفع المستند بنجاح', [
            'id' => $document->id,
            'document_type_id' => $document->document_type_id,
            'description' => $document->description,
            'path' => $document->path,
            'url' => asset('storage/' . $document->path),
            'created_at' => $document->created_at,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $profile = $request->user()->profile;
        if (! $profile) {
            return apiError('الملف الشخصي لمزود الخدمة غير موجود', null, 404);
        }

        $document = $profile->documents()->find($id);
        if (! $document) {
            return apiError('المستند غير موجود', null, 404);
        }

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return apiSuccess('تم حذف المستند بنجاح');
    }

    public function getProjectDocuments(Request $request, int $projectId)
    {
        $project = Project::find($projectId);
        if (! $project) {
            return apiError('المشروع غير موجود', null, 404);
        }

        if ($project->client_id !== $request->user()->id) {
            return apiError('غير مصرح بالوصول إلى مستندات هذا المشروع', null, 403);
        }

        $documents = $project->documents()
            ->with('documentType')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Document $document) {
                return [
                    'id' => $document->id,
                    'document_type_id' => $document->document_type_id,
                    'document_type_name' => $document->documentType?->name,
                    'description' => $document->description,
                    'path' => $document->path,
                    'url' => asset('storage/' . $document->path),
                    'created_at' => $document->created_at,
                ];
            });

        return apiSuccess('تم استرجاع مستندات المشروع بنجاح', $documents);
    }

    public function storeProjectDocument(Request $request, int $projectId)
    {   
        $project = Project::find($projectId);
        if (! $project) {
            return apiError('المشروع غير موجود', null, 404);
        }

        if ($project->client_id !== $request->user()->id) {
            return apiError('غير مصرح برفع مستند لهذا المشروع', null, 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10000',
            'document_type_id' => 'required|exists:document_types,id',
            'description' => 'nullable|string|max:255',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = $project->documents()->create([
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'document_type_id' => $validated['document_type_id'],
        ]);

        return apiSuccess('تم رفع المستند للمشروع بنجاح', [
            'id' => $document->id,
            'document_type_id' => $document->document_type_id,
            'description' => $document->description,
            'path' => $document->path,
            'url' => asset('storage/' . $document->path),
            'created_at' => $document->created_at,
        ]);
    }

    public function destroyProjectDocument(Request $request, int $id)
    {
        $document = Document::find($id);
        if (! $document) {
            return apiError('المستند غير موجود', null, 404);
        }

        if ($document->documentable_type !== Project::class) {
            return apiError('المستند غير مرفق بمشروع', null, 403);
        }

        $project = $document->documentable;
        if (! $project || $project->client_id !== $request->user()->id) {
            return apiError('غير مصرح بحذف هذا المستند', null, 403);
        }

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return apiSuccess('تم حذف مستند المشروع بنجاح');
    }

    public function getProviderDocuments(int $profileId)
    {
        $profile = Profile::find($profileId);
        if (! $profile) {
            return apiError('الملف الشخصي للمزود غير موجود', null, 404);
        }

        $documents = $profile->documents()
            ->with('documentType')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Document $document) {
                return [
                    'id' => $document->id,
                    'document_type_id' => $document->document_type_id,
                    'document_type_name' => $document->documentType?->name,
                    'description' => $document->description,
                    'path' => $document->path,
                    'url' => asset('storage/' . $document->path),
                    'created_at' => $document->created_at,
                ];
            });

        return apiSuccess('تم استرجاع مستندات المزود بنجاح', $documents);
    }

    public function adminDestroyDocument(int $id)
    {
        $document = Document::find($id);
        if (! $document) {
            return apiError('المستند غير موجود', null, 404);
        }

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return apiSuccess('تم حذف المستند بنجاح');
    }
}