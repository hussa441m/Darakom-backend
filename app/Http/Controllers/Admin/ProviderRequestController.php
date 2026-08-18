<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProviderRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $search = trim((string) $request->query('search', ''));

        $query = User::query()
            ->where('type', 'provider')
            ->with([
                'province',
                'contacts.contactType',
                'profile.role',
                'profile.documents.documentType',
            ])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile.role', function ($roleQuery) use ($search) {
                        $roleQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $data = [
            'pending_count' => User::where('type', 'provider')->where('status', 'pending')->count(),
            'providers' => $query->paginate(15),
        ];

        return apiSuccess('تم جلب طلبات مزودي الخدمة بنجاح', $data);
    }

    public function approve(Request $request, User $provider)
    {
        if ($provider->type !== 'provider') {
            return apiError('نوع المستخدم غير صالح. يجب أن يكون مزود خدمة.', null, 400);
        }

        $provider->status = 'active';
        $provider->save();

        if ($request->filled('admin_comment') && $provider->profile) {
            $provider->profile->admin_comment = $request->input('admin_comment');
            $provider->profile->save();
        }

        return apiSuccess('تم قبول الطلب وتفعيل حساب مزود الخدمة بنجاح', $provider->load('profile'));
    }

    public function reject(Request $request, User $provider)
    {
        if ($provider->type !== 'provider') {
            return apiError('نوع المستخدم غير صالح. يجب أن يكون مزود خدمة.', null, 400);
        }

        $validator = Validator::make($request->all(), [
            'admin_comment' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return apiError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $provider->status = 'closed';
        $provider->save();

        if ($provider->profile) {
            $provider->profile->admin_comment = $request->input('admin_comment');
            $provider->profile->save();
        }

        return apiSuccess('تم رفض الطلب بنجاح', $provider->load('profile'));
    }
}
