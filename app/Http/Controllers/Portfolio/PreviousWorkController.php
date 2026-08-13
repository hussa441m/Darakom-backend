<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\PreviousWork;
use App\Models\PreviousWorkImage;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PreviousWorkController extends Controller
{
    /**
     * Fetch all previous works.
     * If profileId is provided, fetch for that specific provider profile (public access).
     * Otherwise, fetch for the authenticated provider's own profile.
     */
    public function index(Request $request, $profileId = null)
    {
        try {
            // Determine which profile to fetch previous works for
            if ($profileId) {
                // Public endpoint: fetch for specific provider (by profileId)
                $profile = Profile::findOrFail($profileId);
            } else {
                // Private endpoint: fetch for authenticated user's profile
                $profile = $request->user()->profile;
                if (!$profile) {
                    return apiError('الملف الشخصي لمزود الخدمة غير موجود', null, 404);
                }
            }

            // Fetch previous works with images, ordered by creation date
            $previousWorks = $profile->previousWorks()
                ->with('images')
                ->orderByDesc('created_at')
                ->get()
                ->map(function (PreviousWork $work) {
                    return $this->formatPreviousWorkData($work);
                });

            return apiSuccess(
                'تم استرجاع الأعمال السابقة بنجاح',
                $previousWorks
            );
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع الأعمال السابقة: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Fetch full details of a specific previous work item with its images.
     * Accessible by all users.
     */
    public function show($id)
    {
        try {
            $previousWork = PreviousWork::with('images', 'profile')
                ->findOrFail($id);

            return apiSuccess(
                'تم استرجاع تفاصيل العمل السابق بنجاح',
                $this->formatPreviousWorkData($previousWork)
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('العمل السابق غير موجود', null, 404);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع تفاصيل العمل: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Create a new previous work item.
     * Allow authenticated providers to create. Optionally support uploading multiple images.
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            ]);

            // Get authenticated user's profile
            $profile = $request->user()->profile;
            if (!$profile) {
                return apiError('الملف الشخصي لمزود الخدمة غير موجود', null, 404);
            }

            // Create the previous work item
            $previousWork = $profile->previousWorks()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => $validated['date'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);

            // Handle image uploads if provided
            if ($request->hasFile('images')) {
                $isCover = true; // First image is the cover
                foreach ($request->file('images') as $image) {
                    $path = $image->store('previous_works', 'public');
                    
                    $previousWork->images()->create([
                        'path' => $path,
                        'is_cover' => $isCover,
                    ]);
                    
                    $isCover = false; // Only first image is cover
                }
            }

            return apiSuccess(
                'تم إنشاء العمل السابق بنجاح',
                $this->formatPreviousWorkData($previousWork->load('images')),
                201
            );
        } catch (ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء إنشاء العمل السابق: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update textual details of a previous work item.
     * Only the owner (authenticated provider) can update their own previous work.
     */
    public function update(Request $request, $id)
    {
        try {
            $previousWork = PreviousWork::findOrFail($id);

            // Authorization check: only owner can update
            $profile = $request->user()->profile;
            if (!$profile || $previousWork->profile_id !== $profile->id) {
                return apiError('غير مصرح بتعديل هذا العمل السابق', null, 403);
            }

            // Validate input (all fields optional for update)
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:5000',
                'date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
            ]);

            // Update only provided fields
            $previousWork->update($validated);

            return apiSuccess(
                'تم تحديث العمل السابق بنجاح',
                $this->formatPreviousWorkData($previousWork->load('images'))
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('العمل السابق غير موجود', null, 404);
        } catch (ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تحديث العمل السابق: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete a previous work item along with all its images.
     * Only the owner (authenticated provider) OR an Admin can delete.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $previousWork = PreviousWork::with('images')->findOrFail($id);

            // Authorization check: only owner or admin can delete
            $user = $request->user();
            $profile = $user->profile;
            $isOwner = $profile && $previousWork->profile_id === $profile->id;
            $isAdmin = $user && $user->type === 'admin';

            if (!$isOwner && !$isAdmin) {
                return apiError('غير مصرح بحذف هذا العمل السابق', null, 403);
            }

            // Delete all associated images from storage
            foreach ($previousWork->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
            }

            // Delete the previous work record (cascade will delete images)
            $previousWork->delete();

            return apiSuccess('تم حذف العمل السابق وجميع صوره بنجاح');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('العمل السابق غير موجود', null, 404);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف العمل السابق: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Format previous work data for API response.
     */
    private function formatPreviousWorkData(PreviousWork $work)
    {
        return [
            'id' => $work->id,
            'title' => $work->title,
            'description' => $work->description,
            'date' => $work->date,
            'location' => $work->location,
            'profile_id' => $work->profile_id,
            'images' => $work->images->map(function (PreviousWorkImage $image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => asset('storage/' . $image->path),
                    'is_cover' => (bool) $image->is_cover,
                    'created_at' => $image->created_at,
                ];
            }),
            'created_at' => $work->created_at,
            'updated_at' => $work->updated_at,
        ];
    }
}
