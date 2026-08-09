<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|confirmed|min:6',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'province_id' => 'required|exists:provinces,id',
            'type' => 'required|in:client,provider',
            'experience_start' => 'required_if:type,provider|date',
            'role_id' => 'required_if:type,provider|exists:roles,id',
            'work_area' => 'required_if:type,provider|string|max:100',

            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:50000',
            'documents.*.type' => 'required|exists:document_types,id',
            'documents.*.description' => 'nullable|string|max:255',
        ]);

        $validated['status'] = $request->type == 'provider' ? 'pending' : 'active';

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'province_id' => $validated['province_id'],
        ]);

        if ($user->type === 'provider') {
            $profile = $user->profile()->create([
                'experience_start' => $validated['experience_start'],
                'work_area' => $validated['work_area'],
                'role_id' => $validated['role_id'],
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $index => $documentData) {
                    if (isset($documentData['file']) && $documentData['file']->isValid()) {
                        $path = $documentData['file']->store('documents', 'public');

                        $type = $request->input("documents.{$index}.type");
                        $description = $request->input("documents.{$index}.description");

                        $profile->documents()->create([
                            'path' => $path,
                            'description' => $description,
                            'document_type_id' => $type,
                        ]);
                    }
                }
            }
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return apiSuccess(
            'تم إنشاء الحساب بنجاح',
            [
                'type' => $user->type,
                'name' => $user->full_name,
                'token' => $token,
            ]
        );
    }

       public function login(Request $request)
       {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return apiError(
                "بيانات الدخول غير صحيحة",
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return apiError(
                "البريد الإلكتروني أو كلمة المرور غير صحيحة"
            );
        }

        if ($user->status === 'closed' || $user->status === 'locked') {
            return apiError(
                "الحساب غير مفعل"
            );
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return apiSuccess("تم تسجيل الدخول بنجاح", [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'type' => $user->type,
            'status' => $user->status,
            'token' => $token
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $validated['email'];
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $otp,
            'created_at' => Carbon::now(),
        ]);

        Mail::raw("رمز التحقق لتغيير كلمة المرور هو: {$otp}. لا تشارك هذا الرمز مع أي شخص.", function ($message) use ($email) {
            $message->to($email)
                ->subject('رمز إعادة تعيين كلمة المرور');
        });

        return apiSuccess('تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني. تحقق من صندوق الوارد.', []);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->where('token', $validated['otp'])
            ->first();

        if (!$record) {
            return apiError('رمز التحقق غير صحيح أو البريد الإلكتروني غير موجود', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $expiresAt = Carbon::parse($record->created_at)->addMinutes(15);
        if (Carbon::now()->greaterThan($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return apiError('انتهت صلاحية رمز إعادة التعيين. يرجى طلب رمز جديد.', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return apiSuccess('تم تغيير كلمة المرور بنجاح');
    }


    public function getProfile(Request $request)
    {
       $user = $request->user()->load([
        'province',
       'profile.role',
       'profile.documents',
       'profile.qualifications',
     ]);

    return apiSuccess(
        'تم جلب بيانات الحساب بنجاح',
        new UserResource($user)
    );
    } 

    public function updateProfile(Request $request)
    {
    $user = $request->user();

       $validated = $request->validate([
    'first_name' => 'required|string|max:50',
    'last_name'  => 'required|string|max:50',
    'email' => [
        'required',
        'email',
        'max:100',
        Rule::unique('users', 'email')->ignore($user->id, 'id'),
    ],
    'province_id' => 'required|exists:provinces,id',

 'address'    => 'nullable|string|max:255',
       // 'province_id' => 'required|exists:provinces,id',

        'experience_start' => $user->type === 'provider'
        ? 'required|date'
        : 'nullable',

        'role_id' => $user->type === 'provider'
        ? 'required|exists:roles,id'
        : 'nullable',

        'work_area' => $user->type === 'provider'
        ? 'required|string|max:100'
        : 'nullable|string|max:100',
        'bio'              => 'nullable|string',
        'syndicate_number' => 'nullable|string|max:50',
    ]);

    $user->update([
        'first_name' => $validated['first_name'],
        'last_name'  => $validated['last_name'],
        'email'      => $validated['email'],
        'address'    => $validated['address'] ?? $user->address,
        'province_id' => $validated['province_id'],
    ]);

    if ($user->type === 'provider' && $user->profile) {

        $user->profile->update([
            'experience_start' => $validated['experience_start'],
            'role_id'          => $validated['role_id'],
            'work_area'        => $validated['work_area'],
            'bio'              => $validated['bio'] ?? $user->profile->bio,
            'syndicate_number' => $validated['syndicate_number'] ?? $user->profile->syndicate_number,
        ]);
    }

    $user->load([
         'province',
        'profile.role',
        'profile.documents',
        'profile.qualifications',
    ]);

    return apiSuccess('تم تعديل الحساب بنجاح',new UserResource($user) );
  }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return apiSuccess('تم تسجيل الخروج بنجاح');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return apiError('كلمة المرور الحالية غير صحيحة', null, 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return apiSuccess('تم تغيير كلمة المرور بنجاح');
    }
}
    