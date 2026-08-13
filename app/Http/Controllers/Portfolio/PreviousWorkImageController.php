<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\PreviousWork;
use App\Models\PreviousWorkImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PreviousWorkImageController extends Controller
{
    /**
     * Upload one or multiple images to an existing previous work item.
     * Only the owner (authenticated provider) can upload images.
     */
    public function store(Request $request, $previousWorkId)
    {
        try {
            // Find the previous work item
            $previousWork = PreviousWork::findOrFail($previousWorkId);

            // Authorization check: only owner can upload images
            $profile = $request->user()->profile;
            if (!$profile || $previousWork->profile_id !== $profile->id) {
                return apiError('غير مصرح برفع صور لهذا العمل السابق', null, 403);
            }

            // Validate images
            $validated = $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5000',
            ]);

            $uploadedImages = [];

            // Process each image
            foreach ($request->file('images') as $image) {
                $path = $image->store('previous_works', 'public');

                $imageRecord = $previousWork->images()->create([
                    'path' => $path,
                    'is_cover' => false, // New images are not cover by default
                ]);

                $uploadedImages[] = $this->formatImageData($imageRecord);
            }

            return apiSuccess(
                'تم رفع الصور بنجاح',
                $uploadedImages,
                201
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('العمل السابق غير موجود', null, 404);
        } catch (ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء رفع الصور: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete a single specific image.
     * Only the owner (authenticated provider) OR an Admin can delete.
     */
    public function destroy(Request $request, $imageId)
    {
        try {
            $image = PreviousWorkImage::findOrFail($imageId);
            $previousWork = $image->previousWork;

            // Authorization check: only owner or admin can delete
            $user = $request->user();
            $profile = $user->profile;
            $isOwner = $profile && $previousWork->profile_id === $profile->id;
            $isAdmin = $user && $user->type === 'admin';

            if (!$isOwner && !$isAdmin) {
                return apiError('غير مصرح بحذف هذه الصورة', null, 403);
            }

            // Delete physical file from storage first
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete database record
            $image->delete();

            return apiSuccess('تم حذف الصورة بنجاح');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('الصورة غير موجودة', null, 404);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف الصورة: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Mark a specific image as the primary cover.
     * Sets is_cover = true for this image and false for all others in the same previous work.
     * Only the owner (authenticated provider) can set cover.
     */
    public function setCover(Request $request, $imageId)
    {
        try {
            $image = PreviousWorkImage::findOrFail($imageId);
            $previousWork = $image->previousWork;

            // Authorization check: only owner can set cover
            $profile = $request->user()->profile;
            if (!$profile || $previousWork->profile_id !== $profile->id) {
                return apiError('غير مصرح بتعيين غلاف لهذا العمل السابق', null, 403);
            }

            // Reset all images' is_cover to false for this previous work
            $previousWork->images()->update(['is_cover' => false]);

            // Set this image as cover
            $image->update(['is_cover' => true]);

            return apiSuccess(
                'تم تعيين الصورة كغلاف بنجاح',
                $this->formatImageData($image->fresh())
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiError('الصورة غير موجودة', null, 404);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تعيين الغلاف: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Format image data for API response.
     */
    private function formatImageData(PreviousWorkImage $image)
    {
        return [
            'id' => $image->id,
            'previous_work_id' => $image->previous_work_id,
            'path' => $image->path,
            'url' => asset('storage/' . $image->path),
            'is_cover' => (bool) $image->is_cover,
            'created_at' => $image->created_at,
        ];
    }
}
