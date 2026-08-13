<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display a paginated list of provider profiles.
     */
    public function index(Request $request)
    {
        try {
            $query = Profile::with(['user', 'role', 'serviceCategories'])
                ->when($request->filled('role_id'), function ($query) use ($request) {
                    $query->where('role_id', $request->role_id);
                })
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->search;
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->orderByDesc('created_at');

            $profiles = $query->paginate($request->get('per_page', 15));

            $profiles->getCollection()->each(function (Profile $profile) {
                $profile->append('average_rating');
            });

            return apiSuccess('تم استرجاع ملفات المزودين بنجاح', $profiles);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع ملفات المزودين: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display full profile details for admin review.
     */
    public function show(Profile $profile)
    {
        try {
            $profile->load(['user', 'role', 'documents', 'qualifications', 'previousWorks', 'serviceCategories']);
            $profile->append('average_rating');

            return apiSuccess('تم استرجاع تفاصيل الملف الشخصي بنجاح', $profile);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء استرجاع تفاصيل الملف الشخصي: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update profile details or admin feedback.
     */
    public function update(Request $request, Profile $profile)
    {
        try {
            $validated = $request->validate([
                'admin_comment' => 'nullable|string|max:1000',
                'role_id' => 'sometimes|required|exists:roles,id',
                'work_area' => 'sometimes|required|string|max:100',
                'syndicate_number' => [
                    'sometimes',
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('profiles', 'syndicate_number')->ignore($profile->id),
                ],
                'experience_start' => 'sometimes|required|date',
            ]);

            $profile->fill($validated);
            $profile->save();

            $profile->load(['user', 'role', 'documents', 'qualifications', 'previousWorks', 'serviceCategories']);
            $profile->append('average_rating');

            return apiSuccess('تم تحديث الملف الشخصي بنجاح', $profile);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiError('خطأ في البيانات المدخلة', $e->errors(), 422);
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء تحديث الملف الشخصي: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete a profile record and clean up related data.
     */
    public function destroy(Profile $profile)
    {
        try {
            if ($profile->documents()->exists()) {
                $profile->documents()->delete();
            }

            if ($profile->qualifications()->exists()) {
                $profile->qualifications()->delete();
            }

            if ($profile->previousWorks()->exists()) {
                $profile->previousWorks()->delete();
            }

            $profile->delete();

            return apiSuccess('تم حذف الملف الشخصي بنجاح');
        } catch (\Exception $e) {
            return apiError('حدث خطأ أثناء حذف الملف الشخصي: ' . $e->getMessage(), null, 500);
        }
    }
}
