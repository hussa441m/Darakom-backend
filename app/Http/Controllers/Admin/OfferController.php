<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * عرض جميع العروض للأدمن مع البحث والفلترة
     */
    public function index(Request $request)
    {
        $query = Offer::with([
            'project:id,title',
            'provider.user:id,name,first_name,last_name',
            'provider.role',
            'documents'
        ]);

        // الفلترة حسب حالة العرض (قيد المراجعة، مقبول، مرفوض)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث باسم المزود أو اسم المشروع
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('project', function ($p) use ($search) {
                    $p->where('title', 'like', "%{$search}%");
                })->orWhereHas('provider.user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // جلب البيانات مع الترقيم الصفحي (Pagination)
        $offers = $query->latest()->paginate($request->get('per_page', 15));

        return apiSuccess("قائمة العروض", $offers);
    }

    /**
     * قبول العرض من قبل الإدارة
     */
    public function approve(Offer $offer)
    {
        if ($offer->status === 'accepted') {
            return apiError('تم قبول هذا العرض مسبقاً.');
        }

        $offer->update([
            'status' => 'accepted',
        ]);

        return apiSuccess('تم قبول العرض بنجاح.', $offer);
    }

    /**
     * رفض العرض مع ذكر السبب
     */
    public function reject(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $offer->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'], // مربوط بالحقل الموجود في مودل Offer
        ]);

        return apiSuccess('تم رفض العرض وتسجيل السبب.', $offer);
    }
}