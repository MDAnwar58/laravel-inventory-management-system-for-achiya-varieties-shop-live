<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Handlers\File;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.admin.profile.base');
    }


    public function store_or_update(ProfileStoreRequest $request)
    {
        try {
            // Update user
            $user = auth()->user();
            $data = $request->validated();

            // Handle avatar upload
            if ($avatar = File::store($request, 'avatar', 'avatars', $user))
                $user->avatar = $avatar;
            $user->name = $data['name'];
            $user->email = $data['email'] ?? $user->email;
            $user->phone_number = $data['phone_number'] ?? $user->phone_number;
            $user->save();

            $profile = Profile::updateOrCreate([
                'user_id' => $user->id
            ], [
                'user_id' => $user->id,
                'city' => $data['city'] ?? $user->profile?->city,
                'zip_code' => $data['zip_code'] ?? $user->profile?->zip_code,
                'present_address' => $data['present_address'] ?? $user->profile?->present_address,
                'address' => $data['address'] ?? $user->profile?->address,
            ]);
            // how to marse with profile card front side and card back side 
            $fileData = [];

            if ($cardFrontSide = File::store($request, 'card_front_side', 'card_front_sides', $profile)) {
                $fileData['card_front_side'] = $cardFrontSide;
            }
            if ($cardBackSide = File::store($request, 'card_back_side', 'card_back_sides', $profile)) {
                $fileData['card_back_side'] = $cardBackSide;
            }
            if (count($fileData) > 0)
                $profile->update($fileData);

            return redirect()->route('admin.profile')
                ->with([
                    'status' => 'success',// must be: success, error, info, or warning
                    'msg' => 'Your Profile updated!'
                ]);

        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return redirect()->back()
                ->with([
                    'status' => 'error',
                    'msg' => 'An error occurred while updating the profile. Please try again.'
                ])
                ->withInput();
        }
    }

}
