<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\AcceptClient;
use App\Notifications\ChangeState;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    
    
    public function index(Request $request)
    {
        $status = $request->status;
        $profiles = User::where('type' , 'client')
        ->when($status != "*" , function ($q) use ($status) {
            return $q->where('status',  $status);
        })->with('profile.role' , 'profile.user')
        ->with('profile.documents' )->get();
        //   return $profiles;              
        return apiSuccess('العملاء ',   UserResource::collection($profiles));
    }

    public function ChangeClientState(User $user , Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,locked,pending'
        ]);
        $user->update(['status' => $request->status]);
        
        $user->notify(new ChangeState($request->status));
        return apiSuccess("تم  تغيير حالة الحساب");
    }
}
