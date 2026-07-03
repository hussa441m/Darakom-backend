<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:175|unique:users',
            'password' => 'required|confirmed|min:6',
            'type' => 'required|in:client,customer',
            'experience_start' => 'required_if:type,client|date',
            'role_id' => 'required_if:type,client|exists:roles,id',
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,png,jpeg,png,webp|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'required|max:255',
        ]);

        $validated['status'] = $request->type == 'client' ? 'pending' : 'active';

        $user = User::create(
            $validated
        );
        $type = $user->type;
        $name = $user->name;
        $token = $user->createToken("mobile")->plainTextToken;

        if ($type == 'client') {
            $profile = $user->profile()->create([
                'experience_start' => $validated['experience_start'],
                'role_id' => $validated['role_id'],
            ]);
            if ($request->has('documents')) {
                foreach ($request->documents as $document) {

                    $docName = $document['file']->store('projects', 'public');

                    $profile->documents()->create([
                        'path' => $docName,
                        'description' => $document['description'],
                        'document_type_id' => $document['type'],
                    ]);
                }
            }
        }
        return apiSuccess('تم إنشاء الحساب بنجاح ', compact('type', 'name', 'token'));
    }

    function getProfile(Request $request)
    {
        $user = $request->user();
        $user->experience_start = $user->profile->experience_start ?? null;
        $user->experience_start = $user->profile->experience_start ?? null;
        $user->role_id = $user->profile->role?->id ?? null;
        $user->role = $user->profile->role?->name ?? null;
        return apiSuccess('تم جلب بيانات الحساب بنجاح ', $user);
    }
    function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:175|unique:users,email,' . $user->id,
            'experience_start' => 'required_if:type,client|date',
            'role_id' => 'required_if:type,client|exists:roles,id',
        ]);

        $user->update(
            $validated
        );

        if ($user->type == 'client') {
            $profile = $user->profile()->update([
                'experience_start' => $validated['experience_start'],
                'role_id' => $validated['role_id'],
            ]);
        }
        return apiSuccess('تم تعديل الحساب بنجاح ', compact('user'));
    }

    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return apiError("invalid inputs", $validator->messages(),  Response::HTTP_UNPROCESSABLE_ENTITY);

        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            $type = $user->type;

            $name =  $user->name;
            $token = $user->createToken("web api")->plainTextToken;
            return apiSuccess("Account login successfuly", compact('type', 'name', 'token'), Response::HTTP_CREATED);
        }
        return apiError("اسم المستخدم أو كلمة المرور غير صحيحة");
    }

    function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return apiSuccess("logout ok");
    }
}
