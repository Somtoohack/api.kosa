<?php
namespace App\Http\Controllers\User;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Models\Profile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{

    public function createProfile(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone_number'  => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'lga'           => 'required|string',
            'address'       => 'required|string',
            'date_of_birth' => 'required|date',
        ]);

        try {
            $user = Auth::user();

            if (! $user) {
                return $this->sendMessage('Invalid session', ErrorCodes::INVALID_SESSION);
            }

            if ($user->profile) {
                return $this->sendMessage('User profile already exists', ErrorCodes::PROFILE_ALREADY_EXISTS);
            }
            $user->profile()->create([
                'first_name'    => $request->first_name,
                'middle_name'   => $request->middle_name,
                'last_name'     => $request->last_name,
                'phone_number'  => $request->phone_number,
                'device_key'    => $request->device_key,
                'device_name'   => $request->device_name,
                'state'         => $request->state,
                'city'          => $request->city,
                'lga'           => $request->lga,
                'address'       => $request->address,
                'date_of_birth' => $request->date_of_birth,
            ]);

            //Create a postman request forr this

            // Reload the user object from the database
            $user = Auth::user()->fresh();

            return $this->sendResponse(
                [
                    'user'  => new UserProfileResource($user),
                    'token' => getValidatedToken($request),
                ],
                'User profile created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('An error occurred while creating the user profile', [], ErrorCodes::TRY_AGAIN);
        }
    }
    public function createTag(Request $request)
    {
        $request->validate([
            'user_tag' => 'required|string|max:15|unique:user_profiles,user_tag',
        ], [
            'user_tag.unique' => 'tag is unavailable', // Custom error message
        ]);

        try {
            $user = Auth::user();
            if (! $user) {
                return $this->sendMessage('Invalid session', ErrorCodes::INVALID_SESSION);
            }

            if ($user->profile->user_tag) {
                return $this->sendMessage('User tag already exists', ErrorCodes::TRY_AGAIN);
            }
            $user->profile->update([
                'user_tag' => $request->user_tag,
            ]);

            $user = Auth::user()->fresh();

            return $this->sendResponse(
                [
                    'user'  => new UserProfileResource($user),
                    'token' => getValidatedToken($request),
                ],
                'User Tag created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('An error occurred while creating the user tag' . $e, [], ErrorCodes::TRY_AGAIN);
        }
    }
}