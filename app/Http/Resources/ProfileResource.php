<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->user?->full_name,

            'role' => $this->role?->name,

            'work_area' => $this->work_area,

            'bio' => $this->bio,

            'experience' => $this->experience_start
                ? round(
                    Carbon::parse($this->experience_start)
                        ->diffInDays(now()) / 365.25,
                    1
                )
                : 0,

            'experience_years' => $this->experience_years,

            'syndicate_number' => $this->syndicate_number,

            'logo' => $this->logo,

            'qualifications' => $this->qualifications->map(function ($qualification) {

                return [
                    'id' => $qualification->id,
                    'name' => $qualification->name,
                    'image' => asset('storage/'.$qualification->image),
                ];

            }),

            'documents' => $this->documents->map(function ($doc) {

                return [
                    'id' => $doc->id,
                    'url' => asset('storage/'.$doc->path),
                ];

            }),

        ];
    }
}