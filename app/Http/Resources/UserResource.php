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

            'name' => $this->full_name,

            'email' => $this->email,

            'type' => $this->type,

            'status' => $this->status,

            'avatar' => $this->avatar,


            'profile' => $this->whenLoaded('profile', function () {

                return [

                    'id' => $this->profile->id,

                    'role' => $this->profile->role?->name,

                    'work_area' => $this->profile->work_area,

                    'bio' => $this->profile->bio,

                    'experience' => $this->profile->experience_start
                        ? round(
                            Carbon::parse($this->profile->experience_start)
                                ->diffInDays(now()) / 365.25,
                            1
                        )
                        : 0,

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