<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user()
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->updateProfile($request->user(), $request->validated());

        return redirect()->route('profile.edit')->with('success', 'Profile has been Updated!');
    }

    public function storeStaff(StoreStaffRequest $request): RedirectResponse
    {
        try {
            $this->profileService->createStaff($request->user(), $request->validated());
            return redirect()->route('profile.edit')->with('success', 'Account has Been Created!');
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')->with('error', $e->getMessage());
        }
    }

    public function toggleStaff(Request $request, int $id): RedirectResponse
    {
        try {
            $this->profileService->toggleStaffStatus($request->user(), $id);
            return redirect()->route('profile.edit')->with('success', 'Status has been changed!');
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')->with('error', $e->getMessage());
        }
    }
}
