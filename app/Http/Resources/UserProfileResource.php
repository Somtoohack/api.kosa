<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $firstName = $this->profile->first_name ?? '';
        $middleName = $this->profile->middle_name ?? '';
        $lastName = $this->profile->last_name ?? '';

        $fullName = '';
        if (!empty($firstName) || !empty($middleName) || !empty($lastName)) {
            $fullName = trim("$firstName $middleName $lastName");
        }

        return [
            'email' => $this->email,
            'user_tag' => $this->profile->user_tag ?? null,
            'profile' => [
                'first_name' => $this->profile->first_name ?? $this->name,
                'last_name' => $this->profile->last_name ?? null,
                'middle_name' => $this->profile->middle_name ?? null,
                'full_name' => $fullName,
                'phone_number' => $this->profile->phone_number ?? null,
                'profile_image' => $this->profile->profile_image ?? null,
                'date_of_birth' => $this->profile->date_of_birth ?? null,
            ],
            'contact' => [
                'country' => getCountryDetails($this->country),
                'state' => $this->profile->state ?? null,
                'lga' => $this->profile->lga ?? null,
                'address' => $this->profile->address ?? null,
            ],
            'kyc' => [
                'bvn' => $this->kyc->bvn ?? null,
                'nin' => $this->kyc->nin ?? null,
                'is_email_verified' => !empty($this->email_verified_at) ? true : false,
                'user_tag_set' => !empty($this->profile->user_tag) ? true : false,
                'is_bvn_validated' => $this->kyc->bvn_validated ?? false,
                'is_nin_validated' => $this->kyc->nin_validated ?? false,
                'authorization_pin_set' => $this->authorizationPin()->exists(),
            ],
            'keys' => [
                'user_key' => $this->user_key,
                'device_key' => $this->profile->device_key ?? null,
            ],
        ];

    }
}