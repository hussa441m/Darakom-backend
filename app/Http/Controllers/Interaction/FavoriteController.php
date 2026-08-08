<?php

namespace App\Http\Controllers\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $favorites = $user->favorites()
            ->with('favoriteUser.profile')
            ->get();

        return apiSuccess('المفضلة', $favorites);
    }

    public function toggle(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'favorite_user_id' => 'required|exists:users,id',
        ]);

        if ($validated['favorite_user_id'] == $user->id) {
            return apiError('لا يمكنك إضافة نفسك إلى المفضلة.');
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('favorite_user_id', $validated['favorite_user_id'])
            ->first();

        if ($favorite) {
            $favorite->delete();
            return apiSuccess('تم إزالة المستخدم من المفضلة بنجاح.');
        }

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'favorite_user_id' => $validated['favorite_user_id'],
        ]);

        return apiSuccess('تم إضافة المستخدم إلى المفضلة بنجاح.', $favorite);
    }

    public function destroy(Request $request, $favoriteUserId)
    {
        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('favorite_user_id', $favoriteUserId)
            ->first();

        if (!$favorite) {
            return apiError('هذا المستخدم غير موجود في المفضلة الخاصة بك.', null, 404);
        }

        $favorite->delete();

        return apiSuccess('تم إزالة المستخدم من المفضلة بنجاح.');
    }
}
