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
            'name' => $this->full_name ?? trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')),
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'status' => $this->status,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
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

                    'documents' => $this->profile->relationLoaded('documents') 
                        ? $this->profile->documents->map(function ($doc) {
                            return [
                                'id' => $doc->id,
                                'description' => $doc->description,
                                'type' => $doc->documentType?->name,
                                'url' => asset('storage/' . $doc->path),
                            ];
                        }) 
                        : [],

                    'qualifications' => $this->profile->relationLoaded('qualifications') 
                        ? $this->profile->qualifications->map(function ($qualification) {
                            return [
                                'id' => $qualification->id,
                                'name' => $qualification->name,
                                'image' => $qualification->image ? asset('storage/' . $qualification->image) : null,
                            ];
                        }) 
                        : [],
                ];
            }),
        ];
    }
}