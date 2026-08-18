<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            
            // 👇 تمت إضافة هذه الحقول لحل مشكلة الفرونت إند
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->full_name,

            'email' => $this->email,
            'phone' => $this->phone,
            
            // 👇 تمت إضافة حقل العنوان لحل مشكلة اختفائه بعد التحديث
            'address' => $this->address,

            'type' => $this->type,

            'status' => $this->status,

            'avatar' => $this->avatar,
            'province_id' => $this->province_id,
            'province' => $this->province?->name, 

            'profile' => $this->whenLoaded('profile', function () {

                return [

                    'id' => $this->profile->id,

                    'role' => $this->profile->role?->name,

                    'work_area' => $this->profile->work_area,

                    'bio' => $this->profile->bio,

                    'experience' => $this->profile->experience_start
                    ? Carbon::parse($this->profile->experience_start)->diffInYears(now())
                    : ($this->profile->experience_years ?? 0),

                    'syndicate_number' => $this->profile->syndicate_number,

                    'documents' => $this->profile->documents->map(function ($doc) {

                        return [
                            'id' => $doc->id,
                            'url' => asset('storage/'.$doc->path),
                        ];

                    }),

                    'qualifications' => $this->profile->qualifications->map(function ($qualification) {

                        return [
                            'id' => $qualification->id,
                            'name' => $qualification->name,
                            'image' => asset('storage/'.$qualification->image),
                        ];

                    }),

                ];

            }),

        ];
    }
}