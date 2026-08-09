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
            return apiError('Service category not found', 404);
        }

        return apiSuccess('Providers loaded successfully', $category);
    }

    public function toggleProviderService(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
        ]);

        $profile = $request->user()->profile;

        if (! $profile) {
            return apiError('Provider profile not found', 404);
        }

        $result = $profile->serviceCategories()->toggle($validated['service_category_id']);
        $action = count($result['attached']) ? 'attached' : (count($result['detached']) ? 'detached' : 'untouched');

        return apiSuccess("Service category {$action} successfully", [
            'profile_id' => $profile->id,
            'service_category_id' => $validated['service_category_id'],
            'action' => $action,
        ]);
    }
}
